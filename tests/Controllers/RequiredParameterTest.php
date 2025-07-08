<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Controllers;

use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequiredPostException;

class RequiredParameterTest extends ControllerTestCase
{
    #[Test]
    public function testRequiredParameterWithoutValidInput(): void
    {
        $router = $this->setupRouter(
            'POST',
            '/parameter/post/required',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value]
        );

        $this->expectException(MissingRequiredPostException::class);
        $router->route()->getBody()->getContents();
    }

    #[Test]
    public function testRequiredParameterWithValidInput(): void
    {
        $data = 'post';

        $router = $this->setupRouter(
            'POST',
            '/parameter/post/required/',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value],
            [],
            [],
            [],
            [
                'test' => $data
            ]
        );

        $response = $router->route()->getBody()->getContents();

        $this->assertEquals($data, $response);
    }
    #[Test]
    public function testNotRequiredParameterWithoutValidInput(): void
    {
        $router = $this->setupRouter(
            'POST',
            '/parameter/post/not-required',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value]
        );

        $response = $router->route()->getBody()->getContents();
        $this->assertEquals('default', $response);
    }

    #[Test]
    public function testNotRequiredParameterWithValidInput(): void
    {
        $data = 'post';

        $router = $this->setupRouter(
            'POST',
            '/parameter/post/not-required/',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value],
            [],
            [],
            [],
            [
                'test' => $data
            ]
        );

        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($data, $response);
    }
}
