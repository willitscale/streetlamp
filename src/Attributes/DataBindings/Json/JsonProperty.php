<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\DataBindings\Json;

use Attribute;
use willitscale\Streetlamp\Exceptions\Validators\InvalidParameterFailedToPassFilterValidation;
use willitscale\Streetlamp\Attributes\DataBindings\DataBindingObjectInterface;
use willitscale\Streetlamp\Attributes\Validators\ValidatorInterface;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;
use ReflectionClass;
use ReflectionProperty;
use ReflectionNamedType;
use ReflectionIntersectionType;
use ReflectionUnionType;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
readonly class JsonProperty implements JsonAttribute
{
    use JsonMatchesType;

    public function __construct(
        private bool $required = true,
        private ?string $alias = null
    ) {
    }

    public function buildProperty(object $instance, ReflectionProperty $property, mixed $jsonValue): void
    {
        $key = (empty($this->alias)) ? $property->getName() : $this->alias;

        if ($this->required && empty($jsonValue->{$key})) {
            $className = get_class($instance);
            throw new InvalidParameterTypeException(
                "JS001",
                "Parameter $key in $className is required, but not passed."
            );
        }

        $value = $jsonValue->{$key} ?? null;

        $attributes = $property->getAttributes();

        foreach ($attributes as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof ValidatorInterface) {
                if (!$attributeInstance->validate($value)) {
                    throw new InvalidParameterFailedToPassFilterValidation(
                        "PR003",
                        "Parameter {$key} failed to pass the filter validation"
                    );
                }
                $value = $attributeInstance->sanitize($value);
            }
        }

        $types = match (get_class($property->getType())) {
            ReflectionNamedType::class => [$property->getType()],
            ReflectionIntersectionType::class,
            ReflectionUnionType::class => $property->getType()->getTypes()
        };

        $typeMatches = false;

        foreach ($types as $type) {
            if (!$this->matchesType($type, $value)) {
                continue;
            }

            if (!$type->isBuiltin()) {
                $propertyReflectionClass = new ReflectionClass($type->getName());
                $attributes = $propertyReflectionClass->getAttributes();

                foreach ($attributes as $attribute) {
                    $attributeInstance = $attribute->newInstance();
                    if ($attributeInstance instanceof DataBindingObjectInterface) {
                        $value = $attributeInstance->getObject($propertyReflectionClass, $value);
                    }
                }
            }

            $typeMatches = true;
            break;
        }

        if (!$typeMatches) {
            $className = get_class($instance);
            $valueType = gettype($value);
            $propertyName = $property->getType() instanceof ReflectionNamedType
                ? $property->getType()->getName()
                : 'unknown type';
            $message = "Parameter $key in $className is of type {$propertyName}, but value is of type {$valueType}.";
            throw new InvalidParameterTypeException(
                "JS002",
                $message
            );
        }

        $property->setValue($instance, $value);
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }
}
