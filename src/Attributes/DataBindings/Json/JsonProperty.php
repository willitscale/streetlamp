<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\DataBindings\Json;

use Attribute;
use willitscale\Streetlamp\Attributes\DataBindings\DataBindingObjectInterface;
use willitscale\Streetlamp\Attributes\Validators\ValidatorInterface;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;
use ReflectionClass;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
readonly class JsonProperty implements JsonAttribute
{
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

        $value = $jsonValue->{$key};

        $attributes = $property->getAttributes();

        foreach ($attributes as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof ValidatorInterface) {
                $attributeInstance->validate($value);
                $value = $attributeInstance->sanitize($value);
            }
        }

        if (!$property->getType()->isBuiltin()) {
            $propertyReflectionClass = new ReflectionClass($property->getType()->getName());
            $attributes = $propertyReflectionClass->getAttributes();

            foreach ($attributes as $attribute) {
                $attributeInstance = $attribute->newInstance();
                if ($attributeInstance instanceof DataBindingObjectInterface) {
                    $value = $attributeInstance->getObject($propertyReflectionClass, $value);
                }
            }
        }

        $property->setValue($instance, $value);
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }
}
