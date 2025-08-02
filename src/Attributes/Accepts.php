<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes;

use Attribute;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Accepts implements RouteContract
{
    public function __construct(private string|MediaType $mediaType)
    {
    }

    public function applyToController(Controller $controller): void
    {
        $controller->addAccepts($this->getMediaType());
    }

    public function applyToRoute(Route $route): void
    {
        $route->addAccepts($this->getMediaType());
    }

    public function getMediaType(): string
    {
        if ($this->mediaType instanceof MediaType) {
            return $this->mediaType->value;
        }
        return $this->mediaType;
    }
}
