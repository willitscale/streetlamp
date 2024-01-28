<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\DataBindings\Json;

use Attribute;
use ReflectionClass;
use ReflectionProperty;
use willitscale\Streetlamp\Attributes\DataBindings\ArrayMapInterface;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;
use willitscale\Streetlamp\Exceptions\Json\ClassIsNotJsonObjectException;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class JsonArray implements ArrayMapInterface, JsonAttribute
{
    use JsonDataBinding;

    public function __construct(
        private readonly string $className,
        readonly private bool $required = true,
        readonly private string|null $alias = null
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
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
        $property->setValue($instance, $this->map($value));
    }

    public function map(array $value): array
    {
        $reflectionClass = new ReflectionClass($this->className);
        $reflectionAttributes = $reflectionClass->getAttributes(JsonObject::class);

        if (empty($reflectionAttributes)) {
            throw new ClassIsNotJsonObjectException(
                "JA001",
                "Class {$this->className} is not a JsonObject."
            );
        }

        $instances = [];
        foreach ($value as $data) {
            $instances [] = $this->getObject($reflectionClass, $data);
        }

        return $instances;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }
}
