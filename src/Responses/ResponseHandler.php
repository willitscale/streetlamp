<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Responses;

use DI\Container;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;
use willitscale\Streetlamp\Exceptions\Json\InvalidJsonObjectParameter;
use willitscale\Streetlamp\Exceptions\Validators\InvalidParameterFailedToPassFilterValidation;
use willitscale\Streetlamp\Models\Route;
use willitscale\Streetlamp\ResponseTypes\ResponseTypeInterface;

readonly class ResponseHandler implements RequestHandlerInterface
{
    public function __construct(
        private Route $route,
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
            throw new Exception("Middleware must implement Psr\Http\Server\MiddlewareInterface");
        }

        return $middleware->process($request, $this);
    }

    public function response(ServerRequestInterface $request): ResponseInterface
    {
        $args = array_map(function ($parameter) use ($request) {
            try {
                return $parameter->getValue($this->matches, $request);
            } catch (InvalidParameterFailedToPassFilterValidation $e) {
                throw $e;
            } catch (InvalidJsonObjectParameter $e) {
                throw $e;
            } catch (InvalidParameterTypeException $e) {
                throw $e;
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

        $response = $this->container->make($this->route->getResponseType());

        if (!($response instanceof ResponseTypeInterface)) {
            throw new Exception("Response Type must implement " . ResponseTypeInterface::class);
        }

        return $response->execute(
            $this->route,
            $request,
            $args
        );
    }
}
