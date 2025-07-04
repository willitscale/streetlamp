<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes;

use Attribute;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Middleware implements AttributeContract
{
    public function __construct(private string $middleware)
    {
    }

    public function applyToController(Controller $controller): void
    {
        $controller->addMiddleware($this->middleware);
    }

    public function applyToRoute(Route $route): void
    {
        $route->addMiddleware($this->middleware);
    }
}
