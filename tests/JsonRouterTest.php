<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests;

use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;
use willitscale\StreetlampTest\RouteTestCase;

class JsonRouterTest extends RouteTestCase
{
    const TEST_BODY_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'TestApp' . DIRECTORY_SEPARATOR . 'json.test.dat';
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

        file_put_contents(self::TEST_BODY_FILE, json_encode($testData));

        $router = $this->setupRouter(
            'POST',
            '/json/array',
            MediaType::APPLICATION_JSON->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $response = $router->route(true);
        $this->assertEquals($testData, json_decode($response, true));
    }

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

        file_put_contents(self::TEST_BODY_FILE, json_encode($testData));

        $router = $this->setupRouter(
            'POST',
            '/json/array',
            MediaType::APPLICATION_JSON->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $this->expectException(InvalidParameterTypeException::class);
        $router->route(true);
    }
}
