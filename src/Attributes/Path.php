<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes;

use Attribute;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
readonly class Path implements RouteContract
{
    public function __construct(private string $path)
    {
    }

    public function applyToController(Controller $controller): void
    {
        $controller->setPath($this->path);
    }

    public function applyToRoute(Route $route): void
    {
        $route->appendPath($this->path);
    }
}
