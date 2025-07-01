<?php

namespace willitscale\Streetlamp\Responses;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use willitscale\Streetlamp\Exceptions\InvalidRouteResponseException;
use willitscale\Streetlamp\Models\Route;
use willitscale\Streetlamp\Requests\Stream;
use willitscale\Streetlamp\RouteBuilder;

readonly class ResponseHandler implements RequestHandlerInterface
{
    public function __construct(
        private Route $route,
        private RouteBuilder $routeBuilder,
        private Container $container,
        private array $matches = [],
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (empty($this->route->getMiddleware())) {
            return $this->response($request);
        }

        $middleware = $this->route->popMiddleware();
        $middleware = $this->container->make($middleware);

        // TODO: Custom exception for this
        if (!($middleware instanceof MiddlewareInterface)) {
            throw new \Exception("Middleware must implement Psr\Http\Server\MiddlewareInterface");
        }

        return $middleware->process($request, $this);
    }

    public function response(ServerRequestInterface $request): ResponseInterface
    {
        $args = array_map(function ($parameter) {
            return $parameter->getValue($this->matches);
        }, $this->route->getParameters());

        try {
            return $this->restoreResponse($args);
        } catch (Throwable $e) {
            // Should we log this?
        }

        $requestArgument = [
            'request' => $request
        ];

        $application = $this->container->make(
            $this->route->getClass(),
            $requestArgument
        );

        $response = $this->container->call(
            [
                $application,
                $this->route->getFunction()
            ],
            array_merge($requestArgument, $args)
        );

        if (!isset($response) || !($response instanceof ResponseInterface)) {
            unset($response);
            throw new InvalidRouteResponseException(
                'R001',
                'Call to ' . $this->route->getClass() . '::' .
                $this->route->getFunction() . ' did not return a Response object.'
            );
        }

        try {
            $this->storeResponse($args, $response);
        } catch (Throwable $e) {
            // Should we log this?
        }

        return $response;
    }

    private function restoreResponse(array $args): ResponseInterface
    {
        $cacheRule = $this->route->getCacheRule();

        if (is_null($cacheRule)) {
            // TODO: this should be a custom exception
            throw new \Exception('No cache rule enabled');
        }

        $cacheHandler = $this->routeBuilder->getRouterConfig()->getCacheHandler();

        $key = $cacheRule->getKey($this->route, $args);

        if (!$cacheHandler->exists($key)) {
            // TODO: this should be a custom exception
            throw new \Exception('Cache key does not exist: ' . $key);
        }

        $data = $cacheHandler->retrieveAndDeserialize($key);

        $stream = new Stream('php://temp', 'rw+');
        $stream->write($data['contents']);

        return $data['response']->withBody($stream);
    }

    private function storeResponse(array $args, ResponseInterface $response): void
    {
        $cacheRule = $this->route->getCacheRule();

        if (is_null($cacheRule)) {
            // TODO: this should be a custom exception
            throw new \Exception('No cache rule enabled');
        }

        $cacheHandler = $this->routeBuilder->getRouterConfig()->getCacheHandler();

        $key = $cacheRule->getKey($this->route, $args);
        $ttl = $cacheRule->getCacheTtl();

        $data = [
            'response' => $response,
            'contents' => $response->getBody()->getContents(),
        ];

        $cacheHandler->serializeAndStore($key, $data, false, $ttl);
    }
}
