<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes;

use Attribute;
use n3tw0rk\Streetlamp\Enums\MediaType;
use n3tw0rk\Streetlamp\Models\Controller;
use n3tw0rk\Streetlamp\Models\Route;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
readonly class Accepts implements AttributeContract
{
    public function __construct(private string|MediaType $mediaType) {
    }

    public function applyToController(Controller $controller): void
    {
        $controller->setAccepts($this->getMediaType());
    }

    public function applyToRoute(Route $route): void
    {
        $route->setAccepts($this->getMediaType());
    }

    public function getMediaType(): string
    {
        if ($this->mediaType instanceof MediaType) {
            return $this->mediaType->value;
        }
        return $this->mediaType;
    }
}
