<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\DataBindings\Json;

use Attribute;
use willitscale\Streetlamp\Attributes\DataBindings\DataBindingObjectInterface;
use ReflectionClass;
use stdClass;
use ReflectionNamedType;
use ReflectionIntersectionType;
use ReflectionUnionType;

#[Attribute(Attribute::TARGET_CLASS)]
class JsonObject implements DataBindingObjectInterface
{
    use JsonMatchesType;
    use JsonDataBinding;

    public function build(ReflectionClass $reflectionClass, string $data): object
    {
        $jsonData = json_decode($data);
        return $this->getObject($reflectionClass, $jsonData);
    }

    public function getSerializable(ReflectionClass $reflectionClass, object $object): mixed
    {
        $data = new stdClass();

        foreach ($reflectionClass->getProperties() as $property) {
            $name = $property->getName();
            $propertyAttributes = $property->getAttributes();
            $isJsonProperty = false;

            foreach ($propertyAttributes as $attribute) {
                $attributeInstance = $attribute->newInstance();
                if ($attributeInstance instanceof JsonIgnore) {
                    $isJsonProperty = false;
                    break;
                } elseif ($attributeInstance instanceof JsonAttribute) {
                    $isJsonProperty = true;
                    $name = $attributeInstance->getAlias() ?? $name;
                }
            }

            if (!$isJsonProperty) {
                continue;
            }

            $value = $property->getValue($object);

            $types = match (get_class($property->getType())) {
                ReflectionNamedType::class => [$property->getType()],
                ReflectionIntersectionType::class,
                ReflectionUnionType::class => $property->getType()->getTypes()
            };

            foreach ($types as $type) {
                if (!$this->matchesType($type, $value)) {
                    continue;
                }

                if (!$type->isBuiltin()) {
                    $innerReflectionClass = new ReflectionClass($value);
                    $reflectionAttributes = $innerReflectionClass->getAttributes(JsonObject::class);

                    if (empty($reflectionAttributes)) {
                        continue;
                    }

                    foreach ($reflectionAttributes as $attribute) {
                        $attributeInstance = $attribute->newInstance();
                        if ($attributeInstance instanceof DataBindingObjectInterface) {
                            $value = $attributeInstance->getSerializable($innerReflectionClass, $value);
                        }
                    }
                }
            }

            $data->{$name} = $value;
        }

        return $data;
    }
}
