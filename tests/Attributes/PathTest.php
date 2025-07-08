<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    #[Test]
    #[DataProvider('validAcceptAnnotations')]
    public function testProcessRouteAnnotationExtractsValidPathFromAnnotationAndBindsItToTheRoute(
        string $expected
    ): void {
        $pathAnnotation = new Path($expected);
        $route = new Route('Test', 'test');
        $pathAnnotation->applyToRoute($route);
        $this->assertEquals($expected, $route->getPath());
    }

    #[Test]
    #[DataProvider('validAcceptAnnotations')]
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
    public static function validAcceptAnnotations(): array
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
