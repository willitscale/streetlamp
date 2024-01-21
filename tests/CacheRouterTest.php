<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests;

use willitscale\Streetlamp\Enums\MediaType;
use willitscale\StreetlampTest\RouteTestCase;

class CacheRouterTest extends RouteTestCase
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
}
