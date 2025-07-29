<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Controllers;

use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\StreetlampTests\TestApp\Usecase\CustomAttributes;

class CustomAttributeTest extends ControllerTestCase
{
    #[Test]
    public function testCustomAttribute(): void
    {
        $expected = [
            [
                'name' => 'attribute1',
                'description' => 'This is attribute 1',
                'class' => CustomAttributes::class,
                'method' => 'attribute1',
            ],
            [
                'name' => 'attribute2',
                'description' => 'This is attribute 2',
                'class' => CustomAttributes::class,
                'method' => 'attribute2',
            ]
        ];

        $router = $this->setupRouter(
            HttpMethod::GET,
            '/custom-attribute',
            $this->getTestRoot(),
            $this->getComposerTestFile()
        );

        $response = json_decode(
            $router->route()->getBody()->getContents(),
            true
        );

        $this->assertEquals($expected, $response);
    }
}
