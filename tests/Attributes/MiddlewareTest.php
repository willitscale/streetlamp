<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Attributes\Middleware;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;
use PHPUnit\Framework\TestCase;

class MiddlewareTest extends TestCase
{
    #[Test]
    #[DataProvider('validAnnotations')]
    public function testProcessRouteAnnotationCorrectlyAndExtractThePostFlightClass(
        array $expectedClasses
    ): void {
        $route = new Route('Test', 'test');

        foreach ($expectedClasses as $expectedClass) {
            $middleware = new Middleware($expectedClass);
            $middleware->applyToRoute($route);
        }

        $middleware = $route->getMiddleware();

        $this->assertCount(count($expectedClasses), $middleware);

        for ($i = 0; $i < count($expectedClasses); $i++) {
            $this->assertEquals($expectedClasses[$i], $middleware[$i]);
        }
    }

    #[Test]
    #[DataProvider('validAnnotations')]
    public function testProcessControllerAnnotationCorrectlyAndExtractThePostFlightClass(
        array $expectedClasses
    ): void {
        $controller = new Controller('Test', 'test');

        foreach ($expectedClasses as $expectedClass) {
            $middleware = new Middleware($expectedClass);
            $middleware->applyToController($controller);
        }

        $middleware = $controller->getMiddleware();

        $this->assertCount(count($expectedClasses), $middleware);

        for ($i = 0; $i < count($expectedClasses); $i++) {
            $this->assertEquals($expectedClasses[$i], $middleware[$i]);
        }
    }

    public static function validAnnotations(): array
    {
        return [
            'it should extract the class and namespace from a valid annotation' => [
                'expectedClasses' => [
                    'Test/Test'
                ]
            ],
            'it should extract just the class from a valid annotation' => [
                'expectedClasses' => [
                    'Test'
                ]
            ],
            'it should extract just the class and namepsace with multiple levels from a valid annotation' => [
                'expectedClasses' => [
                    'Level1/Level2/Test'
                ]
            ]
        ];
    }
}
