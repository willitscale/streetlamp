<?php

declare(strict_types=1);

namespace willitscale\StreetlampTest;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\SimpleCache\CacheInterface;
use willitscale\Streetlamp\Builders\RouteBuilder;
use willitscale\Streetlamp\Builders\RouterConfigBuilder;
use willitscale\Streetlamp\CacheHandlers\NullCacheHandler;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Requests\ServerRequest;
use willitscale\Streetlamp\Requests\Uri;
use willitscale\Streetlamp\Router;

class RouteTestCase extends TestCase
{
    public function setupRouter(
        string|HttpMethod $method,
        string $path,
        string $rootDirectory,
        string $composerFile,
        ?StreamInterface $body = null,
        array $headers = [],
        array $serverParams = [],
        array $cookieParams = [],
        array $queryParams = [],
        array $postParams = [],
        array $filesParams = [],
        string $protocolVersion = '1.1',
        CacheInterface $routeCacheHandler = new NullCacheHandler(),
    ): Router {
        $routerConfig = new RouterConfigBuilder()
            ->setComposerFile($composerFile)
            ->setRouteCached(false)
            ->setRootDirectory($rootDirectory)
            ->setRethrowExceptions(true)
            ->setRouteCacheHandler($routeCacheHandler)
            ->setRequest(new ServerRequest(
                $method->value ?? $method,
                new Uri($path),
                $body,
                $headers,
                $protocolVersion,
                $serverParams,
                $cookieParams,
                $queryParams,
                $filesParams,
                $postParams
            ))
            ->build();

        $routeBuilder = new RouteBuilder(
            $routerConfig
        );

        return new Router($routeBuilder);
    }
}
