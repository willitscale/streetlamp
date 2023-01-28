<?php declare(strict_types=1);

namespace willitscale\Streetlamp\Models;

use willitscale\Streetlamp\Attributes\Parameter\Parameter;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Exceptions\Attributes\InvalidParameterAlreadyBoundException;
use willitscale\Streetlamp\Requests\RequestInterface;

class Route extends Context
{
    /**
     * @param string $class
     * @param string $function
     * @param string|null $path
     * @param HttpMethod|null $method
     * @param string|null $accepts
     * @param array $parameters
     * @param array $preFlight
     * @param array $postFlight
     */
    public function __construct(
        string                  $class,
        private string          $function,
        string|null             $path = null,
        private HttpMethod|null $method = null,
        string|null             $accepts = null,
        private array           $parameters = [],
        array                   $preFlight = [],
        array                   $postFlight = []
    ) {
        parent::__construct($class, $path, $accepts, $preFlight, $postFlight);
    }

    /**
     * @return string
     */
    public function getFunction(): string
    {
        return $this->function;
    }

    /**
     * @param string $function
     */
    public function setFunction(string $function): void
    {
        $this->function = $function;
    }

    /**
     * @return string|null
     */
    public function getMethod(): string|null
    {
        if ($this->method instanceof HttpMethod) {
            return $this->method->value;
        }

        return $this->method;
    }

    /**
     * @param HttpMethod $method
     */
    public function setMethod(HttpMethod $method): void
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameter
     */
    public function setParameters(array $parameter): void
    {
        $this->parameters = $parameter;
    }

    /**
     * @param string $parameterName
     * @param Parameter $parameter
     * @return void
     * @throws InvalidParameterAlreadyBoundException
     */
    public function addParameter(string $parameterName, Parameter $parameter): void
    {
        if (array_key_exists($parameterName, $this->parameters)) {
            throw new InvalidParameterAlreadyBoundException("ROUTE001", "Cannot bind the same parameter $parameterName to multiple inputs");
        }

        $this->parameters [$parameterName] = $parameter;
    }

    public function matchesRoute(RequestInterface $request, array &$matches): bool
    {
        $matchesRoute = preg_match_all(
            '#^' . $this->path . '/?$#i',
            $request->getPath(),
            $matches
        );

        return $matchesRoute && $request->getMethod() === $this->getMethod();
    }

    public function matchesContentType(RequestInterface $request): bool
    {
        return !isset($this->accepts) || $request->getContentType() === $this->accepts;
    }
}
