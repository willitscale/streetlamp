<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\DataBindings\Json;

use ReflectionClass;
use Throwable;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;
use willitscale\Streetlamp\Exceptions\Json\InvalidJsonObjectParameter;

trait JsonDataBinding
{
    public function getObject(ReflectionClass $reflectionClass, mixed $data): object
    {
        $instance = $reflectionClass->newInstanceWithoutConstructor();
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            try {
                $jsonProperty = $property->getAttributes(JsonProperty::class);
                $jsonArray = $property->getAttributes(JsonArray::class);

                if (empty($jsonProperty) && empty($jsonArray)) {
                    continue;
                }

                $jsonProperty = empty($jsonArray) ?
                    $jsonProperty : $jsonArray;

                $jsonProperty = $jsonProperty[0]->newInstance();
                $jsonProperty->buildProperty($instance, $property, $data);
            } catch (InvalidParameterTypeException $e) {
                throw $e;
            } catch (Throwable $e) {
                throw new InvalidJsonObjectParameter(
                    'JD001',
                    $e->getMessage(),
                );
            }
        }

        return $instance;
    }
}
