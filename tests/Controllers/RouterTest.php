<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Controllers;

use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Enums\MediaType;

class RouterTest extends ControllerTestCase
{
    #[Test]
    public function testRouterGetMethodWithNoParameters(): void
    {
        $router = $this->setupRouter(
            'GET',
            '/',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value]
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
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::APPLICATION_JSON->value]
        );
        $response = $router->route()->getBody()->getContents();
        $this->assertEquals(json_encode($expectedResponse), $response);
    }

    #[Test]
    public function testRouterPostMethodWithPostParameter(): void
    {
        $data = 'post';
        $router = $this->setupRouter(
            'POST',
            '/',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value],
            [],
            [],
            [],
            ['test' => $data]
        );

        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($data, $response);
    }

    #[Test]
    public function testRouterPutMethodWithPathParameter(): void
    {
        $data = 'put';
        $router = $this->setupRouter(
            'PUT',
            '/' . $data,
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value],
        );
        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($data, $response);
    }

    #[Test]
    public function testRouterDeleteMethodWithQueryStringParameter(): void
    {
        $data = '123';
        $router = $this->setupRouter(
            'DELETE',
            '/',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value],
            [],
            [],
            ['test' => $data]
        );
        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($data, $response);
    }

    #[Test]
    public function testRouterPatchMethodWithHeaderParameter(): void
    {
        $data = 'patch';
        $_SERVER['HTTP_TEST'] = $data;
        $router = $this->setupRouter(
            'PATCH',
            '/',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            [
                'Content-Type' => MediaType::TEXT_HTML->value,
                'test' => $data
            ],
        );
        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($data, $response);
    }
}
