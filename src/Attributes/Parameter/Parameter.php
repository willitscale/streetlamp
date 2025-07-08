<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use willitscale\Streetlamp\Attributes\DataBindings\ArrayMapInterface;
use willitscale\Streetlamp\Attributes\DataBindings\DataBindingObjectInterface;
use willitscale\Streetlamp\Attributes\Validators\ValidatorInterface;
use willitscale\Streetlamp\Exceptions\InvalidParameterArrayExpectedException;
use willitscale\Streetlamp\Exceptions\InvalidParameterJsonExpectedException;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeDefinitionException;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;
use willitscale\Streetlamp\Exceptions\Validators\InvalidParameterFailedToPassFilterValidation;
use ReflectionClass;
use willitscale\Streetlamp\Models\File;
use willitscale\Streetlamp\Requests\ServerRequest;

abstract class Parameter
{
    protected string $type;
    protected ArrayMapInterface $arrayMap;

    public function __construct(
        protected readonly ?string $key,
        protected readonly bool $required = false,
        protected array $validators = []
    ) {
    }

    public function setArrayMap(ArrayMapInterface $arrayMap): void
    {
        $this->arrayMap = $arrayMap;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getRequired(): bool
    {
        return $this->required;
    }

    public function getValue(array $pathMatches, ServerRequest $request): mixed
    {
        return $this->castAndValidateValue($this->value($pathMatches, $request));
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValidators(): array
    {
        return $this->validators;
    }

    public function addValidator(ValidatorInterface $validator): void
    {
        $this->validators[] = $validator;
    }

    abstract public function value(array $pathMatches, ServerRequest $request): string|int|bool|float|array|File;

    protected function castAndValidateValue(mixed $value): mixed
    {
        if (!is_string($value) && !is_array($value) && !$value instanceof File) {
            throw new InvalidParameterTypeException(
                "PR001",
                "Parameter $this->key is not a string or array"
            );
        }

        if (empty($this->type)) {
            throw new InvalidParameterTypeDefinitionException(
                "PR002",
                "Unable to resolve data type for $this->key"
            );
        }

        foreach ($this->validators as $validator) {
            if (!$validator->validate($value)) {
                throw new InvalidParameterFailedToPassFilterValidation(
                    "PR003",
                    "Parameter $this->key failed to pass the filter validation"
                );
            }

            $value = $validator->sanitize($value);
        }

        return match ($this->type) {
            File::class => $value,
            'int' => (int)$value,
            'float' => (float)$value,
            'bool' => (bool)$value,
            'string' => (string)$value,
            'array' => $this->buildArray($value),
            default => $this->buildObject($value, $this->type)
        };
    }

    private function buildArray(mixed $value): array
    {
        if (!isset($this->arrayMap)) {
            return (array) $value;
        }

        $decodedValue = json_decode($value);

        if (!$decodedValue) {
            throw new InvalidParameterJsonExpectedException(
                "PR005",
                "Invalid JSON passed."
            );
        }

        if (!is_array($decodedValue)) {
            throw new InvalidParameterArrayExpectedException(
                "PR006",
                "Invalid JSON passed."
            );
        }

        return $this->arrayMap->map($decodedValue);
    }

    private function buildObject(string $value, string $type): object
    {
        $reflectionClass = new ReflectionClass($type);

        $attributes = $reflectionClass->getAttributes();

        foreach ($attributes as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof DataBindingObjectInterface) {
                return $attributeInstance->build($reflectionClass, $value);
            }
        }

        throw new InvalidParameterTypeDefinitionException(
            "PR004",
            "Parameter $this->key references $type, but it has no data bindings"
        );
    }
}
