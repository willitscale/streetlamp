<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes\Controller;

use Attribute;
use n3tw0rk\Streetlamp\Attributes\AttributeContract;
use n3tw0rk\Streetlamp\Exceptions\Attributes\InvalidAttributeContextException;
use n3tw0rk\Streetlamp\Models\Controller;
use n3tw0rk\Streetlamp\Models\Route;

#[Attribute(Attribute::TARGET_CLASS)]
class RouteController implements AttributeContract
{
    public function applyToController(Controller $controller): void
    {
        $controller->setIsController(true);
    }

    /**
     * @throws InvalidAttributeContextException
     */
    public function applyToRoute(Route $route): void
    {
        throw new InvalidAttributeContextException("RC001", "Cannot define the route as a controller");
    }
}
