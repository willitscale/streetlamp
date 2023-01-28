<?php declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Route;

use Attribute;
use willitscale\Streetlamp\Attributes\AttributeContract;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Exceptions\Attributes\InvalidAttributeContextException;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;

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
