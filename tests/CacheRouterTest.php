<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests;

use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\StreetlampTest\RouteTestCase;

class CacheRouterTest extends RouteTestCase
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

        $router->route();

        $router = $this->setupRouter(
            'GET',
            '/cache/' . $unexpectedCacheValue,
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $response = $router->route()->getBody()->getContents();

        $this->assertEquals($expectedCacheValue, $response);
    }

    #[Test]
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

        $response = $router->route()->getBody()->getContents();

        $this->assertEquals($firstCachedValue, $response);

        $router = $this->setupRouter(
            'GET',
            '/cache/parameter/' . $secondCachedValue,
            MediaType::TEXT_HTML->value,
            __DIR__,
            self::COMPOSER_TEST_FILE
        );

        $response = $router->route()->getBody()->getContents();

        $this->assertEquals($secondCachedValue, $response);
    }
}
