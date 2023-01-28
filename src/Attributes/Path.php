<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes;

use Attribute;
use n3tw0rk\Streetlamp\Models\Controller;
use n3tw0rk\Streetlamp\Models\Route;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
readonly class Path implements AttributeContract
{
    public function __construct(private string $path)
    {}

    public function applyToController(Controller $controller): void
    {
        $controller->setPath($this->path);
    }

    public function applyToRoute(Route $route): void
    {
        $route->appendPath($this->path);
    }
}
