<?php declare(strict_types=1);

namespace Attributes;

use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    /**
     * @test
     * @param string $expected
     * @return void
     * @dataProvider validAcceptAnnotations
     */
    public function testProcessRouteAnnotationExtractsValidPathFromAnnotationAndBindsItToTheRoute(
        string $expected
    ): void {
        $pathAnnotation = new Path($expected);
        $route = new Route('Test', 'test');
        $pathAnnotation->applyToRoute($route);
        $this->assertEquals($expected, $route->getPath());
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
        $pathAnnotation = new Path($expected);
        $controller = new Controller('Test', 'Test');
        $pathAnnotation->applyToController($controller);
        $this->assertEquals($expected, $controller->getPath());
    }

    /**
     * @return array
     */
    public function validAcceptAnnotations(): array
    {
        return [
            'it should correctly match the root path when extracted' => [
                'expected' => "/"
            ],
            'it should correctly match the public path when extracted' => [
                'expected' => "/public"
            ],
            'it should correctly match the image.jpeg path when extracted' => [
                'expected' => "/image.jpeg"
            ],
            'it should correctly match the index.php path when extracted' => [
                'expected' => "/index.php"
            ]
        ];
    }
}
