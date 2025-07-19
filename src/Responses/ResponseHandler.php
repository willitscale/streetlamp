<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Responses;

use DI\Container;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
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
        private LoggerInterface $logger,
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
            throw new Exception("Middleware must implement Psr\Http\Server\MiddlewareInterface");
        }

        return $middleware->process($request, $this);
    }

    public function response(ServerRequestInterface $request): ResponseInterface
    {
        $args = array_map(function ($parameter) use ($request) {
            try {
                return $parameter->getValue($this->matches, $request);
            } catch (Throwable $e) {
                if ($parameter->getRequired()) {
                    throw $e;
                }
            }
            return null;
        }, $this->route->getParameters());

        $args = array_filter($args, function ($value) {
            return !is_null($value);
        });

        try {
            return $this->restoreResponse($args);
        } catch (Throwable $e) {
            $this->logger->warning('Response is not in cache: ' . $e->getMessage());
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
            $this->logger->error('Failed to cache response: ' . $e->getMessage());
        }

        return $response;
    }

    private function restoreResponse(array $args): ResponseInterface
    {
        $cacheRule = $this->route->getCacheRule();

        if (is_null($cacheRule)) {
            // TODO: this should be a custom exception
            throw new Exception('No cache rule enabled');
        }

        $cacheHandler = $this->routeBuilder->getRouterConfig()->getCacheHandler();

        $key = $cacheRule->getKey($this->route, $args);

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

    private function storeResponse(array $args, ResponseInterface $response): void
    {
        $cacheRule = $this->route->getCacheRule();

        if (is_null($cacheRule)) {
            // TODO: this should be a custom exception
            throw new Exception('No cache rule enabled');
        }

        $cacheHandler = $this->routeBuilder->getRouterConfig()->getCacheHandler();

        $key = $cacheRule->getKey($this->route, $args);
        $ttl = $cacheRule->getCacheTtl();

        $data = [
            'response' => $response,
            'contents' => $response->getBody()->getContents(),
        ];

        $cacheHandler->set($key, $data, $ttl);
    }
}
