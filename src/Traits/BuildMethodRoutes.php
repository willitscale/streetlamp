<?php

namespace willitscale\Streetlamp\Traits;

use ReflectionMethod;
use willitscale\Streetlamp\Attributes\AttributeClass;
use willitscale\Streetlamp\Attributes\AttributeContract;
use willitscale\Streetlamp\Attributes\RouteContract;
use willitscale\Streetlamp\Exceptions\MethodParameterNotMappedException;
use willitscale\Streetlamp\Exceptions\NoMethodRouteFoundException;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;
use willitscale\Streetlamp\Models\RouteState;

trait BuildMethodRoutes
{
    use BuildMethodParameters;

    private function buildMethodRoutes(
        RouteState $routeState,
        Controller $controller,
        ReflectionMethod $method
    ): void {
        if (0 === stripos('__', $method->getName())) {
            throw new NoMethodRouteFoundException("Not applying routes to magic methods");
        }

        $attributes = $method->getAttributes();

        if (empty($attributes)) {
            throw new NoMethodRouteFoundException("No attributes defined");
        }

        $route = new Route(
            $controller->getNamespace() . $controller->getClass(),
            $method->getName(),
            $controller->getPath()
        );

        if ($controller->getAccepts()) {
            $route->setAccepts($controller->getAccepts());
        }

        if (!empty($controller->getMiddleware())) {
            $route->setMiddleware($controller->getMiddleware());
        }

        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof RouteContract) {
                $instance->applyToRoute($route);
            }
        }

        foreach ($method->getParameters() as $parameter) {
            try {
                $this->buildMethodParameters($route, $parameter);
            } catch (MethodParameterNotMappedException $e) {
                $this->logger->debug($e->getMessage());
            }
        }

        $routeState->addRoute($route);
    }
}