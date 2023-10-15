<?php

declare(strict_types=1);

namespace willitscale\Streetlamp;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Exceptions\ComposerFileDoesNotExistException;
use willitscale\Streetlamp\Exceptions\ComposerFileInvalidFormatException;
use willitscale\Streetlamp\Exceptions\InvalidContentTypeException;
use willitscale\Streetlamp\Exceptions\InvalidRouteResponseException;
use willitscale\Streetlamp\Exceptions\NoValidRouteException;
use willitscale\Streetlamp\Exceptions\StreetLampRequestException;
use willitscale\Streetlamp\Models\Route;
use willitscale\Streetlamp\Requests\RequestInterface;
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

                $this->preFlight($route, $request);

                $args = [];
                foreach ($route->getParameters() as $key => $parameter) {
                    $args[$key] = $parameter->getValue($matches);
                }

                if ($route->getCacheRule() && $route->getCacheRule()->exists($route)) {
                    $response = $route->getCacheRule()->get($route);
                    $this->postFlight($route, $response);
                    return $response->build($return);
                }

                // Move and refactor this

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

                //

                if ($route->getCacheRule()) {
                    $route->getCacheRule()->store($route, $response);
                }

                $this->postFlight($route, $response);

                $builtResponse =  $response->build($return);

                return $builtResponse;
            }

            if ($pathMatched) {
                throw new InvalidContentTypeException(
                    'R002',
                    'Content type ' . $_SERVER["CONTENT_TYPE"] . ' did not match any matching path routes.'
                );
            }

            throw new NoValidRouteException('R003', 'No valid route found for ' . $request->getPath() . '.');
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

    /**
     * @param Route $route
     * @param RequestInterface $request
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    private function preFlight(Route $route, RequestInterface $request): void
    {
        foreach ($route->getPreFlight() as $preFlight) {
            $flight = $this->container->get($preFlight);
            if ($flight instanceof Flight) {
                $flight->pre($request);
            }
        }
    }

    /**
     * @param Route $route
     * @param ResponseBuilder $response
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    private function postFlight(Route $route, ResponseBuilder $response): void
    {
        foreach ($route->getPostFlight() as $postFlight) {
            $flight = $this->container->get($postFlight);
            if ($flight instanceof Flight) {
                $flight->post($response);
            }
        }
    }
}
