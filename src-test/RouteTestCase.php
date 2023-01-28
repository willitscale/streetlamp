<?php declare(strict_types=1);

namespace willitscale\StreetlampTest;

use Exception;
use willitscale\Streetlamp\Builders\RouterConfigBuilder;
use willitscale\Streetlamp\Requests\CommandLineRequest;
use willitscale\Streetlamp\RouteBuilder;
use willitscale\Streetlamp\RouteCacheHandlers\NullRouteCacheHandler;
use willitscale\Streetlamp\RouteCacheHandlers\RouteCacheHandler;
use willitscale\Streetlamp\Router;
use PHPUnit\Framework\TestCase;

class RouteTestCase extends TestCase
{
    /**
     * @param string $method
     * @param string $path
     * @param string $contentType
     * @param string $rootDirectory
     * @param string $composerFile
     * @param RouteCacheHandler $routeCacheHandler
     * @return Router
     * @throws Exception
     */
    public function setupRouter(
        string $method,
        string $path,
        string $contentType,
        string $rootDirectory,
        string $composerFile,
        RouteCacheHandler $routeCacheHandler = new NullRouteCacheHandler()
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
