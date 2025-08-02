<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models;

use Psr\Http\Message\ServerRequestInterface;
use willitscale\Streetlamp\Attributes\Parameter\Parameter;
use willitscale\Streetlamp\CacheRules\CacheRule;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Exceptions\Attributes\InvalidParameterAlreadyBoundException;

class Route extends Context
{
    public function __construct(
        string $class,
        private string $function,
        ?string $path = null,
        private HttpMethod|null $method = null,
        array $accepts = [],
        private array $parameters = [],
        array $middleware = [],
        array $attributes = [],
        private CacheRule|null $cacheRule = null
    ) {
        parent::__construct($class, $path, $accepts, $middleware, $attributes);
    }

    public function getCacheRule(): ?CacheRule
    {
        return $this->cacheRule;
    }

    public function setCacheRule(?CacheRule $cache): void
    {
        $this->cacheRule = $cache;
    }

    public function getFunction(): string
    {
        return $this->function;
    }

    public function setFunction(string $function): void
    {
        $this->function = $function;
    }

    public function getMethod(): HttpMethod|string|null
    {
        if ($this->method instanceof HttpMethod) {
            return $this->method->value;
        }

        return $this->method;
    }

    public function setMethod(HttpMethod $method): void
    {
        $this->method = $method;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameter): void
    {
        $this->parameters = $parameter;
    }

    public function addParameter(string $parameterName, Parameter $parameter): void
    {
        if (array_key_exists($parameterName, $this->parameters)) {
            throw new InvalidParameterAlreadyBoundException(
                "RM001",
                "Cannot bind the same parameter $parameterName to multiple inputs"
            );
        }

        $this->parameters [$parameterName] = $parameter;
    }

    public function matchesRoute(ServerRequestInterface $request, array &$matches): bool
    {
        $matchesRoute = preg_match_all(
            '#^' . $this->path . '/?$#i',
            $request->getUri()->getPath(),
            $matches
        );

        return $matchesRoute && $request->getMethod() === $this->getMethod();
    }

    public function matchesContentType(ServerRequestInterface $request): bool
    {
        $requestedAccepts = explode(',', $request->getHeaderLine('Content-Type'));
        $requestedAccepts = array_map('trim', $requestedAccepts);
        return empty(array_diff($this->accepts, $requestedAccepts));
    }
}
