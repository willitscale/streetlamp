<?php

declare(strict_types=1);

namespace willitscale\Streetlamp;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Exceptions\ComposerFileDoesNotExistException;
use willitscale\Streetlamp\Exceptions\ComposerFileInvalidFormatException;
use willitscale\Streetlamp\Exceptions\InvalidContentTypeException;
use willitscale\Streetlamp\Exceptions\InvalidRouteResponseException;
use willitscale\Streetlamp\Exceptions\NoValidRouteException;
use willitscale\Streetlamp\Exceptions\StreetLampRequestException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionException;
use willitscale\Streetlamp\Requests\Stream;
use willitscale\Streetlamp\Responses\Response;
use willitscale\Streetlamp\Responses\ResponseHandler;

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
     * @throws ComposerFileDoesNotExistException
     * @throws ComposerFileInvalidFormatException
     * @throws DependencyException
     * @throws InvalidContentTypeException
     * @throws InvalidRouteResponseException
     * @throws NoValidRouteException
     * @throws NotFoundException
     * @throws ReflectionException
     * @throws StreetLampRequestException
     * @throws Throwable
     */
    public function route(): ResponseInterface
    {
        $pathMatched = false;
        $request = $this->routeBuilder->getRouterConfig()->getRequest();
        $stream = new Stream('php://temp', 'rw+');

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

                $responseHandler = new ResponseHandler(
                    $route,
                    $this->routeBuilder,
                    $this->container,
                    $matches
                );

                return $responseHandler->handle($request);
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
                $statusCode = $StreetLampRequestException->getHttpStatusCode();
                $this->logger->error($StreetLampRequestException->getMessage());
                $stream->write($StreetLampRequestException->getMessage());
            }
        } catch (Throwable $exception) {
            if ($this->routeBuilder->getRouterConfig()->isRethrowExceptions()) {
                throw $exception;
            } else {
                $statusCode = HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR;
                $this->logger->error($exception->getMessage());
                $stream->write($exception->getMessage());
            }
        }

        return new Response(
            $stream,
            $statusCode->value
        );
    }

    public function renderRoute(): void
    {
        $response = $this->route();
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $value) {
            header("{$name}: {$value}");
        }
        echo $response->getBody();
    }
}
