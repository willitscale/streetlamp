<?php

declare(strict_types=1);

namespace willitscale\StreetlampTest;

use Exception;
use PHPUnit\Framework\TestCase;
use willitscale\Streetlamp\Builders\RouterConfigBuilder;
use willitscale\Streetlamp\CacheHandlers\CacheHandler;
use willitscale\Streetlamp\CacheHandlers\NullCacheHandler;
use willitscale\Streetlamp\Requests\CommandLineRequest;
use willitscale\Streetlamp\RouteBuilder;
use willitscale\Streetlamp\Router;

class RouteTestCase extends TestCase
{
    /**
     * @param string $method
     * @param string $path
     * @param string $contentType
     * @param string $rootDirectory
     * @param string $composerFile
     * @param CacheHandler $routeCacheHandler
     * @return Router
     * @throws Exception
     */
    public function setupRouter(
        string $method,
        string $path,
        string $contentType,
        string $rootDirectory,
        string $composerFile,
        CacheHandler $routeCacheHandler = new NullCacheHandler()
    ): Router {
        $routerConfig = (new RouterConfigBuilder())
            ->setComposerFile($composerFile)
            ->setCached(false)
            ->setRootDirectory($rootDirectory)
            ->setRethrowExceptions(true)
            ->setRouteCacheHandler($routeCacheHandler)
            ->setRequest(new CommandLineRequest($method, $path, $contentType))
            ->build();

        $routeBuilder = new RouteBuilder(
            $routerConfig
        );

        return new Router($routeBuilder);
    }
}
