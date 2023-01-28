<?php declare(strict_types=1);

namespace n3tw0rk\StreetlampTest;

use Exception;
use n3tw0rk\Streetlamp\Builders\RouterConfigBuilder;
use n3tw0rk\Streetlamp\Requests\CommandLineRequest;
use n3tw0rk\Streetlamp\RouteBuilder;
use n3tw0rk\Streetlamp\RouteCacheHandlers\NullRouteCacheHandler;
use n3tw0rk\Streetlamp\RouteCacheHandlers\RouteCacheHandler;
use n3tw0rk\Streetlamp\Router;
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
