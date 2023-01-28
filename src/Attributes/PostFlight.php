<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes;

use Attribute;
use n3tw0rk\Streetlamp\Flight;
use n3tw0rk\Streetlamp\Models\Controller;
use n3tw0rk\Streetlamp\Models\Route;

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
