<?php declare(strict_types=1);

use n3tw0rk\Streetlamp\Enums\MediaType;
use n3tw0rk\StreetlampTest\RouteTestCase;

class RouterTest extends RouteTestCase
{
    const COMPOSER_TEST_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'TestApp' . DIRECTORY_SEPARATOR . 'composer.test.json';

    /**
     * @return void
     * @throws Exception
     */
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

    /**
     * @return void
     * @throws Exception
     */
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

    /**
     * @return void
     * @throws Exception
     */
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
        UNSET($_POST['test']);
        $this->assertEquals($data, $response);
    }

    /**
     * @return void
     * @throws Exception
     */
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

    /**
     * @return void
     * @throws Exception
     */
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
        UNSET($_GET['test']);
        $this->assertEquals($data, $response);
    }

    /**
     * @return void
     * @throws Exception
     */
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

    /**
     * @return void
     * @throws Exception
     */
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
}
