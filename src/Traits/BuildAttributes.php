<?php

namespace willitscale\Streetlamp\Traits;

use willitscale\Streetlamp\Attributes\AttributeContract;
use willitscale\Streetlamp\Models\RouteState;

trait BuildAttributes
{
    public function buildAttributes(RouteState $routeState, string $root, string $namespace): array
    {
        $attributes = [];
        foreach ($this->getClassesWithAttributes($root, $namespace) as $attributeClass) {
            foreach ($attributeClass->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if ($instance instanceof AttributeContract) {
                    $instance->bind($routeState, $attributeClass);
                }
            }
        }
        return $attributes;
    }
}