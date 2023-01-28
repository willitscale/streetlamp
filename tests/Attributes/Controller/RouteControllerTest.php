<?php declare(strict_types=1);

namespace Attributes\Controller;

use n3tw0rk\Streetlamp\Attributes\Controller\RouteController;
use n3tw0rk\Streetlamp\Exceptions\Attributes\InvalidAttributeContextException;
use n3tw0rk\Streetlamp\Models\Controller;
use n3tw0rk\Streetlamp\Models\Route;
use PHPUnit\Framework\TestCase;

class RouteControllerTest extends TestCase
{
    public function testControllerAttributeAppliesCorrectlyToController(): void {
        $controllerAnnotation = new RouteController();
        $controller = new Controller('Test', 'Test');
        $controllerAnnotation->applyToController($controller);
        $this->assertTrue($controller->isController());
    }

    public function testControllerAttributeFailsToApplyToRoute(): void {
        $this->expectException(InvalidAttributeContextException::class);
        $controllerAnnotation = new RouteController();
        $route = new Route('Test', 'Test');
        $controllerAnnotation->applyToRoute($route);
    }
}
