<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Controllers;

use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\CacheHandlers\FileCacheHandler;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\MediaType;

class CacheRouterTest extends ControllerTestCase
{
    #[Test]
    public function testRouterCacheAlwaysReturnsTheInitialCachedValue(): void
    {
        $expectedCacheValue = 99;
        $unexpectedCacheValue = 23;

        $cacheHandler = new FileCacheHandler();

        $router = $this->setupRouter(
            HttpMethod::GET,
            '/cache/' . $expectedCacheValue,
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value],
            [],
            [],
            [],
            [],
            [],
            '1.1',
            $cacheHandler
        );

        $response = $router->route();

        $router = $this->setupRouter(
            'GET',
            '/cache/' . $unexpectedCacheValue,
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value],
            [],
            [],
            [],
            [],
            [],
            '1.1',
            $cacheHandler
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
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value]
        );

        $response = $router->route()->getBody()->getContents();

        $this->assertEquals($firstCachedValue, $response);

        $router = $this->setupRouter(
            'GET',
            '/cache/parameter/' . $secondCachedValue,
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => MediaType::TEXT_HTML->value]
        );

        $response = $router->route()->getBody()->getContents();

        $this->assertEquals($secondCachedValue, $response);
    }
}
