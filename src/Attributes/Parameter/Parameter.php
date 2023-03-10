<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use willitscale\Streetlamp\Attributes\DataBindings\DataBindingObjectInterface;
use willitscale\Streetlamp\Attributes\Validators\ValidatorInterface;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeDefinitionException;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;
use willitscale\Streetlamp\Exceptions\Validators\InvalidParameterFailedToPassFilterValidation;
use ReflectionClass;

abstract class Parameter
{
    protected string $type;
    protected array $validators = [];

    /**
     * @param string|null $key
     */
    public function __construct(
        protected readonly string|null $key
    ) {
    }

    /**
     * @param array $pathMatches
     * @return string|int|bool|float
     * @throws InvalidParameterTypeException
     * @throws InvalidParameterFailedToPassFilterValidation
     */
    public function getValue(array $pathMatches): mixed
    {
        return $this->castAndValidateValue($this->value($pathMatches));
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return ValidatorInterface[]
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * @param ValidatorInterface $validator
     * @return void
     */
    public function addValidator(ValidatorInterface $validator): void
    {
        $this->validators[] = $validator;
    }

    /**
     * @param array $pathMatches
     * @return string|int|bool|float|array
     */
    abstract public function value(array $pathMatches): string|int|bool|float|array;

    /**
     * @param mixed $value
     * @return mixed
     * @throws InvalidParameterFailedToPassFilterValidation
     * @throws InvalidParameterTypeDefinitionException
     * @throws InvalidParameterTypeException
     */
    protected function castAndValidateValue(mixed $value): mixed
    {
        if (!is_string($value) && !is_array($value)) {
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
            'int' => (int)$value,
            'float' => (float)$value,
            'bool' => (bool)$value,
            'string' => $value,
            'array' => (array)$value,
            default => $this->buildObject($value, $this->type)
        };
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
