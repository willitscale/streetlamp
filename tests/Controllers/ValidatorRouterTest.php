<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Controllers;

use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;
use willitscale\Streetlamp\Exceptions\Validators\InvalidParameterFailedToPassFilterValidation;

class ValidatorRouterTest extends ControllerTestCase
{
    #[Test]
    public function testRouterGetMethodWithPathParameterThatValidatesInput(): void
    {
        $expectedResponse = 99;

        $router = $this->setupRouter(
            'GET',
            '/validator/' . $expectedResponse,
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value]
        );
        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function testRouterDataMappingCorrectlyCreatesAnObject(): void
    {
        $testData = [
            'name' => 'Test',
            'age' => 123
        ];

        $router = $this->setupRouter(
            'POST',
            '/validator/validation',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            $this->createStreamWithContents(json_encode($testData)),
            ['Content-Type' => MediaType::APPLICATION_JSON->value]
        );

        $response = $router->route()->getBody()->getContents();

        $this->assertEquals($testData, json_decode($response, true));
    }

    #[Test]
    public function testRouterDataMappingWithIncorrectDataFailsToCreateObject(): void
    {
        $testData = [
            'name' => 'Test'
        ];

        $router = $this->setupRouter(
            'POST',
            '/validator/validation',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            $this->createStreamWithContents(json_encode($testData)),
            ['Content-Type' => MediaType::APPLICATION_JSON->value]
        );

        $this->expectException(InvalidParameterTypeException::class);
        $router->route();
    }

    #[Test]
    public function testRouterDataMappingCorrectlyCreatesAnArrayOfObjects(): void
    {
        $testData = [
            [
                'name' => 'Test',
                'age' => 123
            ],
            [
                'name' => 'Tester',
                'age' => 456
            ]
        ];

        $router = $this->setupRouter(
            'POST',
            '/validator/validations',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            $this->createStreamWithContents(json_encode($testData)),
            ['Content-Type' => MediaType::APPLICATION_JSON->value]
        );

        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($testData, json_decode($response, true));
    }

    #[Test]
    public function testRouterDataMappingWithIncorrectDataFailsToCreateAnArrayOfObjects(): void
    {
        $testData = [
            [
                'name' => 'Test',
                'age' => 123
            ],
            [
                'name' => 'Tester'
            ]
        ];

        $router = $this->setupRouter(
            'POST',
            '/validator/validations',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            $this->createStreamWithContents(json_encode($testData)),
            ['Content-Type' => MediaType::APPLICATION_JSON->value]
        );

        $this->expectException(InvalidParameterFailedToPassFilterValidation::class);
        $router->route();
    }
}
