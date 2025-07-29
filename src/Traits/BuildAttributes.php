<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Traits;

use willitscale\Streetlamp\Attributes\AttributeClass;
use willitscale\Streetlamp\Attributes\AttributeContract;
use willitscale\Streetlamp\Models\RouteState;

trait BuildAttributes
{
    public function buildAttributes(RouteState $routeState, string $root, string $namespace): void
    {
        foreach ($this->getClassesWithAttributes($root, $namespace) as $attributeClass) {
            foreach ($attributeClass->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if ($instance instanceof AttributeContract) {
                    $instance->bind($routeState, $attributeClass);
                }
            }
            $this->buildMethodAttributes($routeState, $attributeClass);
        }
    }

    public function buildMethodAttributes(RouteState $routeState, AttributeClass $attributeClass): void
    {
        foreach ($attributeClass->getReflection()->getMethods() as $method) {
            foreach ($method->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if ($instance instanceof AttributeContract) {
                    $instance->bind($routeState, $attributeClass, $method->getName());
                }
            }
        }
    }
}
