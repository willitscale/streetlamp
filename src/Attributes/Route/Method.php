<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes\Route;

use Attribute;
use n3tw0rk\Streetlamp\Attributes\AttributeContract;
use n3tw0rk\Streetlamp\Enums\HttpMethod;
use n3tw0rk\Streetlamp\Exceptions\Attributes\InvalidAttributeContextException;
use n3tw0rk\Streetlamp\Models\Controller;
use n3tw0rk\Streetlamp\Models\Route;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class Method implements AttributeContract
{
    public function __construct(private HttpMethod $method)
    {}

    /**
     * @throws InvalidAttributeContextException
     */
    public function applyToController(Controller $controller): void
    {
        throw new InvalidAttributeContextException("MTD001", "Cannot apply a HTTP method to a controller");
    }

    public function applyToRoute(Route $route): void
    {
        $route->setMethod($this->method);
    }
}
