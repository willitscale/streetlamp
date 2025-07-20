<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\ResponseTypes;

use DI\Container;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use willitscale\Streetlamp\Exceptions\InvalidRouteResponseException;
use willitscale\Streetlamp\Models\Route;
use willitscale\Streetlamp\Requests\Stream;
use willitscale\Streetlamp\RouteBuilder;

readonly class HttpMessage implements ResponseTypeInterface
{
    public function __construct(
        private RouteBuilder $routeBuilder,
        private Container $container,
        private LoggerInterface $logger,
    ) {
    }

    public function execute(
        Route $route,
        ServerRequestInterface $request,
        array $args,
    ): ResponseInterface {
        try {
            return $this->restoreResponse($route, $args);
        } catch (Throwable $e) {
            $this->logger->info('Response is not in cache: ' . $e->getMessage());
        }

        $requestArgument = [
            'request' => $request
        ];

        $application = $this->container->make(
            $route->getClass(),
            $requestArgument
        );

        $response = $this->container->call(
            [
                $application,
                $route->getFunction()
            ],
            array_merge($requestArgument, $args)
        );

        if (!isset($response) || !($response instanceof ResponseInterface)) {
            unset($response);
            throw new InvalidRouteResponseException(
                'R001',
                'Call to ' . $route->getClass() . '::' .
                $route->getFunction() . ' did not return a Response object.'
            );
        }

        try {
            $this->storeResponse($route, $args, $response);
        } catch (Throwable $e) {
            $this->logger->error('Failed to cache response: ' . $e->getMessage());
        }

        return $response;
    }

    private function restoreResponse(
        Route $route,
        array $args
    ): ResponseInterface {
        $cacheRule = $route->getCacheRule();

        if (is_null($cacheRule)) {
            // TODO: this should be a custom exception
            throw new Exception('No cache rule enabled');
        }

        $cacheHandler = $this->routeBuilder->getRouterConfig()->getCacheHandler();

        $key = $cacheRule->getKey($route, $args);

        if (!$cacheHandler->has($key)) {
            // TODO: this should be a custom exception
            throw new Exception('Cache key does not exist: ' . $key);
        }

        $data = $cacheHandler->get($key);

        // Instance of check here
        if (!isset($data['response']) || !($data['response'] instanceof ResponseInterface)) {
            unset($data['response']);
            throw new InvalidRouteResponseException(
                'R004',
                'Cached response for key ' . $key . ' is not a valid Response object.'
            );
        }

        $stream = new Stream('php://temp', 'rw+');
        $stream->write($data['contents']);

        return $data['response']->withBody($stream);
    }

    private function storeResponse(
        Route $route,
        array $args,
        ResponseInterface $response
    ): void {
        $cacheRule = $route->getCacheRule();

        if (is_null($cacheRule)) {
            // TODO: this should be a custom exception
            throw new Exception('No cache rule enabled');
        }

        $cacheHandler = $this->routeBuilder->getRouterConfig()->getCacheHandler();

        $key = $cacheRule->getKey($route, $args);
        $ttl = $cacheRule->getCacheTtl();

        $data = [
            'response' => $response,
            'contents' => $response->getBody()->getContents(),
        ];

        $cacheHandler->set($key, $data, $ttl);
    }
}
