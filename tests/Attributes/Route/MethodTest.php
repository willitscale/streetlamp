<?php declare(strict_types=1);

namespace Attributes\Route;

use n3tw0rk\Streetlamp\Attributes\Route\Method;
use n3tw0rk\Streetlamp\Enums\HttpMethod;
use n3tw0rk\Streetlamp\Exceptions\Attributes\InvalidAttributeContextException;
use n3tw0rk\Streetlamp\Models\Controller;
use n3tw0rk\Streetlamp\Models\Route;
use PHPUnit\Framework\TestCase;

class MethodTest extends TestCase
{
    /**
     * @param HttpMethod $expectedMethod
     * @return void
     * @dataProvider validRouteAnnotations
     */
    public function testMethodIsAssignedToTheRouteCorrectly(
        HttpMethod $expectedMethod
    ): void {
        $methodAnnotation = new Method($expectedMethod);
        $route = new Route('Test', 'Test');
        $methodAnnotation->applyToRoute($route);
        $this->assertEquals($route->getMethod(), $expectedMethod->value);
    }

    public function testMethodCannotBeAssignedToController(): void
    {
        $this->expectException(InvalidAttributeContextException::class);
        $methodAnnotation = new Method(HttpMethod::GET);
        $controller = new Controller('Test', 'Test');
        $methodAnnotation->applyToController($controller);
    }

    public function validRouteAnnotations():array
    {
        return [
            'it should identify the GET method and assign it to the route' => [
                'expectedMethod' => HttpMethod::GET
            ],
            'it should identify the POST method and assign it to the route' => [
                'expectedMethod' => HttpMethod::POST
            ],
            'it should identify the PUT method and assign it to the route' => [
                'expectedMethod' => HttpMethod::PUT
            ],
            'it should identify the PATCH method and assign it to the route' => [
                'expectedMethod' => HttpMethod::PATCH
            ],
            'it should identify the DELETE method and assign it to the route' => [
                'expectedMethod' => HttpMethod::DELETE
            ]
        ];
    }
}
