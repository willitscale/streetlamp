<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes;

use PHPUnit\Framework\Attributes\DataProvider;
use willitscale\Streetlamp\Attributes\PreFlight;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;
use PHPUnit\Framework\TestCase;

class PreFlightTest extends TestCase
{
    #[DataProvider('validAnnotations')]
    public function testProcessRouteAnnotationCorrectlyAndExtractThePreflightClass(
        array $expectedClasses
    ): void {
        $route = new Route('Test', 'test');

        foreach ($expectedClasses as $expectedClass) {
            $preflightAnnotation = new PreFlight($expectedClass);
            $preflightAnnotation->applyToRoute($route);
        }

        $preFlights = $route->getPreFlight();
        $this->assertCount(count($expectedClasses), $preFlights);

        for ($i = 0; $i < count($expectedClasses); $i++) {
            $this->assertEquals($expectedClasses[$i], $preFlights[$i]);
        }
    }

    #[DataProvider('validAnnotations')]
    public function testProcessControllerAnnotationCorrectlyAndExtractThePreflightClass(
        array $expectedClasses
    ): void {
        $controller = new Controller('Test', 'test');

        foreach ($expectedClasses as $expectedClass) {
            $preFlight = new PreFlight($expectedClass);
            $preFlight->applyToController($controller);
        }

        $preFlights = $controller->getPreFlight();
        $this->assertCount(count($expectedClasses), $preFlights);

        for ($i = 0; $i < count($expectedClasses); $i++) {
            $this->assertEquals($expectedClasses[$i], $preFlights[$i]);
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
            'it should extract just the class and namespace with multiple levels from a valid annotation' => [
                'expectedClasses' => [
                    'Level1/Level2/Test'
                ]
            ]
        ];
    }
}
