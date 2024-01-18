<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\DataBindings\Json;

use Attribute;
use ReflectionClass;
use willitscale\Streetlamp\Attributes\DataBindings\ArrayMapInterface;
use willitscale\Streetlamp\Exceptions\Json\ClassIsNotJsonObjectException;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class JsonArray implements ArrayMapInterface
{
    public function __construct(private readonly string $className)
    {
    }

    public function getClassName(): string
    {
        return $this->className;
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
            $instance = $reflectionClass->newInstanceWithoutConstructor();
            $properties = $reflectionClass->getProperties();

            foreach ($properties as $property) {
                $jsonProperties = $property->getAttributes(JsonProperty::class);
                if (empty($jsonProperties)) {
                    continue;
                }
                $jsonProperty = $jsonProperties[0]->newInstance();
                $jsonProperty->buildProperty($instance, $property, $data);
            }

            $instances []= $instance;
        }

        return $instances;
    }
}
