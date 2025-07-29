<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes;

use willitscale\Streetlamp\Models\RouteState;

interface AttributeContract
{
    public function bind(
        RouteState $routeState,
        AttributeClass $attributeClass,
        ?string $method = null
    ): void;
}
