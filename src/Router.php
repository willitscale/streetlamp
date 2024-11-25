<?php

declare(strict_types=1);

namespace willitscale\Streetlamp;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Exceptions\ComposerFileDoesNotExistException;
use willitscale\Streetlamp\Exceptions\ComposerFileInvalidFormatException;
use willitscale\Streetlamp\Exceptions\InvalidContentTypeException;
use willitscale\Streetlamp\Exceptions\InvalidRouteResponseException;
use willitscale\Streetlamp\Exceptions\NoValidRouteException;
use willitscale\Streetlamp\Exceptions\StreetLampRequestException;
use willitscale\Streetlamp\Models\Route;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionException;

readonly class Router
{
    /**
     * @param RouteBuilder $routeBuilder
     * @param Container $container
     * @param LoggerInterface $logger
     */
    public function __construct(
        private RouteBuilder $routeBuilder = new RouteBuilder(),
        private Container $container = new Container(),
        private LoggerInterface $logger = new NullLogger()
    ) {
    }

    /**
     * @param bool $return
     * @return string|null
     * @throws ComposerFileDoesNotExistException
     * @throws ComposerFileInvalidFormatException
     * @throws DependencyException
     * @throws InvalidContentTypeException
     * @throws InvalidRouteResponseException
     * @throws NoValidRouteException
     * @throws NotFoundException
     * @throws ReflectionException
     * @throws StreetLampRequestException
     */
    public function route(bool $return = false): null|string
    {
        $pathMatched = false;

        $request = $this->routeBuilder->getRouterConfig()->getRequest();

        try {
            foreach ($this->routeBuilder->getRoutes() as $route) {
                $matches = [];

                if (!$route->matchesRoute($request, $matches)) {
                    continue;
                }

                $pathMatched = true;

                if (!$route->matchesContentType($request)) {
                    continue;
                }

                // TODO: PART 1 Recursively call this with the response build as the final call
                $this->middleware($route, $request);

                $args = [];
                foreach ($route->getParameters() as $key => $parameter) {
                    $args[$key] = $parameter->getValue($matches);
                }

                $cacheRule = $route->getCacheRule();
                $cacheHandler = $this->routeBuilder->getRouterConfig()->getCacheHandler();

                if ($cacheRule) {
                    $key = $cacheRule->getKey($route, $args);
                    if ($cacheHandler->exists($key)) {
                        $response = $cacheHandler->retrieveAndDeserialize($key);
                        // TODO: PART 2 Callback maybe?
                        return $response->build($return);
                    }
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

                if (!isset($response) || !($response instanceof ResponseBuilder)) {
                    throw new InvalidRouteResponseException(
                        'R001',
                        'Call to ' . $route->getClass() . '::' .
                        $route->getFunction() . ' did not return a Response object.'
                    );
                }

                if ($cacheRule) {
                    $key = $cacheRule->getKey($route, $args);
                    $ttl = $cacheRule->getCacheTtl();
                    $cacheHandler->serializeAndStore($key, $response, false, $ttl);
                }

                $this->postFlight($route, $response);

                return $response->build($return);
            }

            if ($pathMatched) {
                throw new InvalidContentTypeException(
                    'R002',
                    'Content type ' . $_SERVER["CONTENT_TYPE"] . ' did not match any matching path routes.'
                );
            }

            throw new NoValidRouteException('R003', 'No valid route found for ' . $request->getUri() . '.');
        } catch (StreetLampRequestException $StreetLampRequestException) {
            if ($this->routeBuilder->getRouterConfig()->isRethrowExceptions()) {
                throw $StreetLampRequestException;
            } else {
                http_response_code($StreetLampRequestException->getHttpStatusCode()->value);
                $this->logger->error($StreetLampRequestException->getMessage());
            }
        } catch (Exception $exception) {
            if ($this->routeBuilder->getRouterConfig()->isRethrowExceptions()) {
                throw $exception;
            } else {
                http_response_code(HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR->value);
                $this->logger->error($exception->getMessage());
            }
        }

        return null;
    }

    private function middleware(
        Route $route,
        ServerRequestInterface $request,
        RequestHandlerInterface $requestHandler
    ): void {
        $middleware = $route->popMiddleware();
        $middleware->process($request, $requestHandler);
    }
}
