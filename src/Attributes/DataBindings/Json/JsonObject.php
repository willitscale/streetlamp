<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes\DataBindings\Json;

use Attribute;
use n3tw0rk\Streetlamp\Attributes\DataBindings\DataBindingObjectInterface;
use ReflectionClass;
use stdClass;

#[Attribute(Attribute::TARGET_CLASS)]
class JsonObject implements DataBindingObjectInterface
{
    public function build(ReflectionClass $reflectionClass, string $data): object
    {
        $jsonData = json_decode($data);
        return $this->getObject($reflectionClass, $jsonData);
    }

    public function getObject(ReflectionClass $reflectionClass, mixed $data): object
    {
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

        return $instance;
    }

    public function getSerializable(ReflectionClass $reflectionClass, object $object): mixed
    {
        $data = new stdClass;

        foreach ($reflectionClass->getProperties() as $property) {

            $name = $property->getName();

            $propertyAttributes = $property->getAttributes();

            $isJsonProperty = false;

            foreach($propertyAttributes as $attribute) {
                $attributeInstance = $attribute->newInstance();
                if ($attributeInstance instanceof JsonIgnore) {
                    $isJsonProperty = false;
                    break;
                } elseif ($attributeInstance instanceof JsonProperty) {
                    $isJsonProperty = true;
                    $name = $attributeInstance->getAlias() ?? $name;
                }
            }

            if (!$isJsonProperty) {
                continue;
            }

            $value = $property->getValue($object);

            if (!$property->getType()->isBuiltin()) {

                $innerReflectionClass = new ReflectionClass($value);
                $reflectionAttributes = $innerReflectionClass->getAttributes(JsonObject::class);

                if (empty($reflectionAttributes)) {
                    continue;
                }

                foreach($reflectionAttributes as $attribute) {
                    $attributeInstance = $attribute->newInstance();
                    if ($attributeInstance instanceof DataBindingObjectInterface) {
                        $value = $attributeInstance->getSerializable($innerReflectionClass, $value);
                    }
                }
            }

            $data->{$name} = $value;
        }

        return $data;
    }
}
