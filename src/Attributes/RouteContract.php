<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes;

use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;

interface RouteContract
{
    public function applyToController(Controller $controller): void;

    public function applyToRoute(Route $route): void;
}
