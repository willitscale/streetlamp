<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Controller;

use Attribute;
use willitscale\Streetlamp\Attributes\RouteContract;
use willitscale\Streetlamp\Exceptions\Attributes\InvalidAttributeContextException;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;

#[Attribute(Attribute::TARGET_CLASS)]
class RouteController implements RouteContract
{
    public function applyToController(Controller $controller): void
    {
        $controller->setIsController(true);
    }

    /**
     * @throws InvalidAttributeContextException
     */
    public function applyToRoute(Route $route): void
    {
        throw new InvalidAttributeContextException("RC001", "Cannot define the route as a controller");
    }
}
