<?php declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes;

use Attribute;
use willitscale\Streetlamp\Flight;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
readonly class PostFlight implements AttributeContract
{
    public function __construct(private string $flight)
    {
    }

    public function applyToController(Controller $controller): void
    {
        $controller->addPostFlight($this->flight);
    }

    public function applyToRoute(Route $route): void
    {
        $route->addPostFlight($this->flight);
    }
}
