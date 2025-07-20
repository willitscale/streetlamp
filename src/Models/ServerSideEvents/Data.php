<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\ServerSideEvents;

use ReflectionClass;
use willitscale\Streetlamp\Attributes\DataBindings\DataBindingObjectInterface;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;

class Data implements ServerSideEvent
{
    public function __construct(
        public string|object $data
    ) {
    }

    public function dispatch(): string
    {
        if (is_object($this->data)) {
            $reflectionClass = new ReflectionClass($this->data);
            $reflectionAttributes = $reflectionClass->getAttributes(JsonObject::class);

            foreach ($reflectionAttributes as $attribute) {
                $attributeInstance = $attribute->newInstance();
                if ($attributeInstance instanceof DataBindingObjectInterface) {
                    $serialized = $attributeInstance->getSerializable($reflectionClass, $this->data);
                    $this->data = json_encode($serialized, JSON_THROW_ON_ERROR);
                }
            }
        }

        return "data: {$this->data}";
    }
}
