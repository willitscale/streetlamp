<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests;

use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\StreetlampTest\RouteTestCase;

class RouterTest extends RouteTestCase
{
    public const string TEST_BODY_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'TestApp' . DIRECTORY_SEPARATOR . 'test.dat';
    public const string COMPOSER_TEST_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'TestApp' . DIRECTORY_SEPARATOR .
        'composer.test.json';

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

    #[Test]
    public function testRouterGetMethodWithNoParameters(): void
    {
        $router = $this->setupRouter(
            'GET',
            '/',
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $response = $router->route()->getBody()->getContents();
        $this->assertEquals('test', $response);
    }

    #[Test]
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
        $response = $router->route()->getBody()->getContents();
        $this->assertEquals(json_encode($expectedResponse), $response);
    }

    #[Test]
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
        $response = $router->route()->getBody()->getContents();
        unset($_POST['test']);
        $this->assertEquals($data, $response);
    }

    #[Test]
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
        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($data, $response);
    }

    #[Test]
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
        $response = $router->route()->getBody()->getContents();
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
        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($data, $response);
    }
}
