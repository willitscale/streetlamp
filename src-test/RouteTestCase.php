<?php

declare(strict_types=1);

namespace willitscale\StreetlampTest;

use Exception;
use PHPUnit\Framework\TestCase;
use willitscale\Streetlamp\Builders\RouterConfigBuilder;
use willitscale\Streetlamp\CacheHandlers\CacheHandler;
use willitscale\Streetlamp\CacheHandlers\NullCacheHandler;
use willitscale\Streetlamp\Requests\ServerRequest;
use willitscale\Streetlamp\Requests\Uri;
use willitscale\Streetlamp\RouteBuilder;
use willitscale\Streetlamp\Router;

class RouteTestCase extends TestCase
{
    public function setupRouter(
        string $method,
        string $path,
        string $contentType,
        string $rootDirectory,
        string $composerFile,
        CacheHandler $routeCacheHandler = new NullCacheHandler()
    ): Router {
        $routerConfig = new RouterConfigBuilder()
            ->setComposerFile($composerFile)
            ->setRouteCached(false)
            ->setRootDirectory($rootDirectory)
            ->setRethrowExceptions(true)
            ->setRouteCacheHandler($routeCacheHandler)
            ->setRequest(new ServerRequest($method, new Uri($path), null, ['Content-Type' => $contentType]))
            ->build();

        $routeBuilder = new RouteBuilder(
            $routerConfig
        );

        return new Router($routeBuilder);
    }
}
