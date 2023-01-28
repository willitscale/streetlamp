<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes;

use n3tw0rk\Streetlamp\Models\Controller;
use n3tw0rk\Streetlamp\Models\Route;

interface AttributeContract
{
    public function applyToController(Controller $controller): void;

    public function applyToRoute(Route $route): void;
}
