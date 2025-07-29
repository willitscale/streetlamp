<?php

namespace willitscale\StreetlampTests\TestApp\Attributes;

use Attribute;
use willitscale\Streetlamp\Attributes\AttributeClass;
use willitscale\Streetlamp\Attributes\AttributeContract;
use willitscale\Streetlamp\Models\RouteState;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_METHOD)]
readonly class CustomAttribute implements AttributeContract
{
    public function __construct(
        private string $name,
        private ?string $description = null,
    ) {
    }

    public function bind(
        RouteState $routeState,
        AttributeClass $attributeClass,
        ?string $method = null
    ): void {
        $routeState->addAttribute([
            'name' => $this->name,
            'description' => $this->description,
            'class' => $attributeClass->getNamespace() . $attributeClass->getClass(),
            'method' => $method,
        ]);
    }
}
