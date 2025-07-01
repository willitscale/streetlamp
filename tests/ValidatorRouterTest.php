<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests;

use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;
use willitscale\Streetlamp\Exceptions\Validators\InvalidParameterFailedToPassFilterValidation;
use willitscale\StreetlampTest\RouteTestCase;

class ValidatorRouterTest extends RouteTestCase
{
    const TEST_BODY_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'TestApp' . DIRECTORY_SEPARATOR . 'test.dat';
    const COMPOSER_TEST_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'TestApp' . DIRECTORY_SEPARATOR . 'composer.test.json';

    public function setUp(): void
    {
        parent::setUp();

        if (file_exists(self::TEST_BODY_FILE)) {
            unlink(self::TEST_BODY_FILE);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (file_exists(self::TEST_BODY_FILE)) {
            unlink(self::TEST_BODY_FILE);
        }
    }

    public function testRouterGetMethodWithPathParameterThatValidatesInput(): void
    {
        $expectedResponse = 99;

        $router = $this->setupRouter(
            'GET',
            '/validator/' . $expectedResponse,
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );
        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($expectedResponse, $response);
    }

    public function testRouterDataMappingCorrectlyCreatesAnObject(): void
    {
        $testData = [
            'name' => 'Test',
            'age' => 123
        ];

        file_put_contents(self::TEST_BODY_FILE, json_encode($testData));

        $router = $this->setupRouter(
            'POST',
            '/validator/validation',
            MediaType::APPLICATION_JSON->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $response = $router->route(true)->getBody()->getContents();

        $this->assertEquals($testData, json_decode($response, true));
    }

    public function testRouterDataMappingWithIncorrectDataFailsToCreateObject(): void
    {
        $testData = [
            'name' => 'Test'
        ];

        $testFile =
            file_put_contents(self::TEST_BODY_FILE, json_encode($testData));

        $router = $this->setupRouter(
            'POST',
            '/validator/validation',
            MediaType::APPLICATION_JSON->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $this->expectException(InvalidParameterTypeException::class);
        $router->route(true);
    }

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

        file_put_contents(self::TEST_BODY_FILE, json_encode($testData));

        $router = $this->setupRouter(
            'POST',
            '/validator/validations',
            MediaType::APPLICATION_JSON->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($testData, json_decode($response, true));
    }

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

        file_put_contents(self::TEST_BODY_FILE, json_encode($testData));

        $router = $this->setupRouter(
            'POST',
            '/validator/validations',
            MediaType::APPLICATION_JSON->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $this->expectException(InvalidParameterFailedToPassFilterValidation::class);
        $router->route(true);
    }
}
