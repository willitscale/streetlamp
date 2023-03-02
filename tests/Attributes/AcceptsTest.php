<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes;

use willitscale\Streetlamp\Attributes\Accepts;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;
use PHPUnit\Framework\TestCase;

class AcceptsTest extends TestCase
{
    /**
     * @test
     * @param string $expected
     * @return void
     * @dataProvider validAcceptAnnotations
     */
    public function testProcessRouteAnnotationExtractsValidMediaTypeFromAcceptAnnotationAndBindsItToTheRoute(
        string $expected
    ): void {
        $acceptsAnnotation = new Accepts($expected);
        $route = new Route('Test', 'test');
        $acceptsAnnotation->applyToRoute($route);
        $this->assertEquals($expected, $route->getAccepts());
    }

    /**
     * @test
     * @param string $expected
     * @return void
     * @dataProvider validAcceptAnnotations
     */
    public function testProcessControllerAnnotationExtractsValidMediaTypeFromAcceptAnnotationAndBindsItToTheController(
        string $expected
    ): void {
        $acceptsAnnotation = new Accepts($expected);
        $controller = new Controller('Test', 'Test');
        $acceptsAnnotation->applyToController($controller);
        $this->assertEquals($expected, $controller->getAccepts());
    }

    /**
     * @return array
     */
    public static function validAcceptAnnotations(): array
    {
        return [
            'it should correctly match a test/test mime type when extracted' => [
                'expected' => "test/test"
            ],
            'it should correctly match a text/html mime type when extracted' => [
                'expected' => "text/html"
            ],
            'it should correctly match a application/json mime type when extracted' => [
                'expected' => "application/json"
            ],
            'it should correctly match a application/xml mime type when extracted' => [
                'expected' => "application/xml"
            ]
        ];
    }
}
