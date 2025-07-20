<?php

namespace willitscale\Streetlamp\Attributes;

use Attribute;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
readonly class ResponseType implements AttributeContract
{
    public function __construct(
        private ?string $responseType = null
    ) {
    }

    public function applyToController(Controller $controller): void
    {
        $controller->setResponseType($this->responseType);
    }

    public function applyToRoute(Route $route): void
    {
        $route->setResponseType($this->responseType);
    }
}
