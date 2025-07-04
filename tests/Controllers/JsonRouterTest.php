<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Controllers;

use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;

class JsonRouterTest extends ControllerTestCase
{
    #[Test]
    public function testJsonArrayCorrectlyMapsJsonBodyToJsonObjectInArray(): void
    {
        $testData = [
            [
                'name' => 'Test',
                'age' => 123
            ],
            [
                'name' => 'Tester',
                'age' => 321
            ]
        ];

        $router = $this->setupRouter(
            'POST',
            '/json/array',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            $this->createStreamWithContents(json_encode($testData)),
            ['Content-Type' => MediaType::APPLICATION_JSON->value]
        );

        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($testData, json_decode($response, true));
    }

    #[Test]
    public function testJsonArrayThrowsExceptionWhenMissingARequiredParameter(): void
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
            '/json/array',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            $this->createStreamWithContents(json_encode($testData)),
            ['Content-Type' => MediaType::APPLICATION_JSON->value]
        );

        $this->expectException(InvalidParameterTypeException::class);
        $router->route();
    }

    #[Test]
    public function testJsonObjectWithNestedArrayMapsCorrectlyToBody(): void
    {
        $testData = [
            'dataTypes' => [
                [
                    'name' => 'Test',
                    'age' => 123
                ],
                [
                    'name' => 'Tester',
                    'age' => 321
                ]
            ],
            'strings' => [
                'one',
                'two',
                'three'
            ]
        ];

        $router = $this->setupRouter(
            'POST',
            '/json/nested',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            $this->createStreamWithContents(json_encode($testData)),
            ['Content-Type' => MediaType::APPLICATION_JSON->value]
        );

        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($testData, json_decode($response, true));
    }

    #[Test]
    public function testJsonObjectWithNestedArrayThrowsExceptionForMissingNestedProperty(): void
    {
        $testData = [
            'dataTypes' => [
                [
                    'name' => 'Test',
                    'age' => 123
                ],
                [
                    'name' => 'Tester'
                ]
            ],
            'strings' => [
                'one',
                'two',
                'three'
            ]
        ];

        $router = $this->setupRouter(
            'POST',
            '/json/nested',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            $this->createStreamWithContents(json_encode($testData)),
            ['Content-Type' => MediaType::APPLICATION_JSON->value]
        );

        $this->expectException(InvalidParameterTypeException::class);
        $router->route();
    }
}
