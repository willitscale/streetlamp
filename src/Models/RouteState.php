<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models;

class RouteState
{
    public function __construct(
        private array $routes = [],
        private array $attributes = [],
    ) {
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function addAttribute(mixed $attribute): void
    {
        $this->attributes[] = $attribute;
    }
}
