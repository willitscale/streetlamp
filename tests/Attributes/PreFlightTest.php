<?php declare(strict_types=1);

namespace Attributes;

use n3tw0rk\Streetlamp\Attributes\PreFlight;
use n3tw0rk\Streetlamp\Models\Controller;
use n3tw0rk\Streetlamp\Models\Route;
use PHPUnit\Framework\TestCase;

class PreFlightTest extends TestCase
{
    /**
     * @test
     * @param array $expectedClasses
     * @return void
     * @dataProvider validAnnotations
     */
    public function testProcessRouteAnnotationCorrectlyAndExtractThePreflightClass(
        array  $expectedClasses
    ): void {
        $route = new Route('Test', 'test');

        foreach($expectedClasses as $expectedClass) {
            $preflightAnnotation = new PreFlight($expectedClass);
            $preflightAnnotation->applyToRoute($route);
        }

        $preFlights = $route->getPreFlight();
        $this->assertCount(count($expectedClasses), $preFlights);

        for ($i = 0; $i < count($expectedClasses); $i++) {
            $this->assertEquals($expectedClasses[$i], $preFlights[$i]);
        }
    }

    /**
     * @test
     * @param array $expectedClasses
     * @return void
     * @dataProvider validAnnotations
     */
    public function testProcessControllerAnnotationCorrectlyAndExtractThePreflightClass(
        array  $expectedClasses
    ): void {
        $controller = new Controller('Test', 'test');

        foreach($expectedClasses AS $expectedClass) {
            $preFlight = new PreFlight($expectedClass);
            $preFlight->applyToController($controller);
        }

        $preFlights = $controller->getPreFlight();
        $this->assertCount(count($expectedClasses), $preFlights);

        for ($i = 0; $i < count($expectedClasses); $i++) {
            $this->assertEquals($expectedClasses[$i], $preFlights[$i]);
        }
    }

    public function validAnnotations(): array
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
