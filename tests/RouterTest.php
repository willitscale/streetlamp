<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests;

use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;
use willitscale\Streetlamp\Exceptions\Validators\InvalidParameterFailedToPassFilterValidation;
use willitscale\StreetlampTest\RouteTestCase;
use function PHPUnit\Framework\assertEquals;

class RouterTest extends RouteTestCase
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

    public function testRouterGetMethodWithNoParameters(): void
    {
        $router = $this->setupRouter(
            'GET',
            '/',
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );
        $response = $router->route(true);
        $this->assertEquals('test', $response);
    }

    public function testRouterGetMethodWithContentTypeJsonAndNoParameters(): void
    {
        $expectedResponse = [
            'test'
        ];
        $router = $this->setupRouter(
            'GET',
            '/json',
            MediaType::APPLICATION_JSON->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );
        $response = $router->route(true);
        $this->assertEquals(json_encode($expectedResponse), $response);
    }

    public function testRouterPostMethodWithPostParameter(): void
    {
        $data = 'post';
        $_POST['test'] = $data;
        $router = $this->setupRouter(
            'POST',
            '/',
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );
        $response = $router->route(true);
        unset($_POST['test']);
        $this->assertEquals($data, $response);
    }

    public function testRouterPutMethodWithPathParameter(): void
    {
        $data = 'put';
        $router = $this->setupRouter(
            'PUT',
            '/' . $data,
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );
        $response = $router->route(true);
        $this->assertEquals($data, $response);
    }

    public function testRouterDeleteMethodWithQueryStringParameter(): void
    {
        $data = '123';
        $_GET['test'] = $data;
        $router = $this->setupRouter(
            'DELETE',
            '/',
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );
        $response = $router->route(true);
        unset($_GET['test']);
        $this->assertEquals($data, $response);
    }

    public function testRouterPatchMethodWithHeaderParameter(): void
    {
        $data = 'patch';
        $_SERVER['HTTP_TEST'] = $data;
        $router = $this->setupRouter(
            'PATCH',
            '/',
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );
        $response = $router->route(true);
        $this->assertEquals($data, $response);
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
        $response = $router->route(true);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testRouterCacheAlwaysReturnsTheInitialCachedValue(): void
    {
        $expectedCacheValue = 99;
        $unexpectedCacheValue = 23;

        $router = $this->setupRouter(
            'GET',
            '/cache/' . $expectedCacheValue,
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $router->route(true);

        $router = $this->setupRouter(
            'GET',
            '/cache/' . $unexpectedCacheValue,
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $response = $router->route(true);

        $this->assertEquals($expectedCacheValue, $response);
    }

    public function testRouterCacheAlwaysReturnsTheParameterCachedValue(): void
    {
        $firstCachedValue = 99;
        $secondCachedValue = 23;

        $router = $this->setupRouter(
            'GET',
            '/cache/parameter/' . $firstCachedValue,
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $response = $router->route(true);

        $this->assertEquals($firstCachedValue, $response);

        $router = $this->setupRouter(
            'GET',
            '/cache/parameter/' . $secondCachedValue,
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $response = $router->route(true);

        $this->assertEquals($secondCachedValue, $response);
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
            '/data/validation',
            MediaType::APPLICATION_JSON->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $response = $router->route(true);

        assertEquals($testData, json_decode($response, true));
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
            '/data/validation',
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
            '/data/validations',
            MediaType::APPLICATION_JSON->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $response = $router->route(true);
        assertEquals($testData, json_decode($response, true));
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
            '/data/validations',
            MediaType::APPLICATION_JSON->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $this->expectException(InvalidParameterFailedToPassFilterValidation::class);
        $router->route(true);
    }
}
