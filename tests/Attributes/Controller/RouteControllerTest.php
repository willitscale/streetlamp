<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Controller;

use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Exceptions\Attributes\InvalidAttributeContextException;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;
use PHPUnit\Framework\TestCase;

class RouteControllerTest extends TestCase
{
    #[Test]
    public function testControllerAttributeAppliesCorrectlyToController(): void
    {
        $controllerAnnotation = new RouteController();
        $controller = new Controller('Test', 'Test');
        $controllerAnnotation->applyToController($controller);
        $this->assertTrue($controller->isController());
    }

    #[Test]
    public function testControllerAttributeFailsToApplyToRoute(): void
    {
        $this->expectException(InvalidAttributeContextException::class);
        $controllerAnnotation = new RouteController();
        $route = new Route('Test', 'Test');
        $controllerAnnotation->applyToRoute($route);
    }
}
