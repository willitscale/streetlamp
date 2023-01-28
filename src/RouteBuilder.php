<?php declare(strict_types=1);

namespace willitscale\Streetlamp;

use willitscale\Streetlamp\Attributes\AttributeContract;
use willitscale\Streetlamp\Attributes\Parameter\Parameter;
use willitscale\Streetlamp\Attributes\Validators\ValidatorInterface;
use willitscale\Streetlamp\Builders\RouterConfigBuilder;
use willitscale\Streetlamp\Exceptions\Attributes\InvalidParameterAlreadyBoundException;
use willitscale\Streetlamp\Exceptions\CacheFileDoesNotExistException;
use willitscale\Streetlamp\Exceptions\CacheFileInvalidFormatException;
use willitscale\Streetlamp\Exceptions\ComposerFileDoesNotExistException;
use willitscale\Streetlamp\Exceptions\ComposerFileInvalidFormatException;
use willitscale\Streetlamp\Exceptions\MethodParameterNotMappedException;
use willitscale\Streetlamp\Exceptions\NoMethodRouteFoundException;
use willitscale\Streetlamp\Exceptions\StreetLampException;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;
use willitscale\Streetlamp\Models\RouterConfig;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;

readonly class RouteBuilder
{
    private RouterConfig|null $routerConfig;

    /**
     * @param RouterConfig|null $routerConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        RouterConfig|null $routerConfig = null,
        private LoggerInterface $logger = new NullLogger()
    ) {
        if (!isset($routerConfig)) {
            $this->routerConfig = (new RouterConfigBuilder())
                ->setConfigFile('router.conf.json')
                ->build();
        } else {
            $this->routerConfig = $routerConfig;
        }
    }

    /**
     * @return RouterConfig
     */
    public function getRouterConfig(): RouterConfig
    {
        return $this->routerConfig;
    }

    /**
     * @return Route[]
     * @throws ComposerFileDoesNotExistException
     * @throws ComposerFileInvalidFormatException
     * @throws ReflectionException
     * @throws InvalidParameterAlreadyBoundException
     */
    public function getRoutes(): array
    {
        try {
            return $this->loadCachedConfig();
        } catch (StreetLampException $StreetLampException) {
            $this->logger->debug($StreetLampException->getMessage());
        }

        return $this->loadConfig();
    }

    /**
     * @return array
     * @throws CacheFileDoesNotExistException
     * @throws CacheFileInvalidFormatException
     */
    private function loadCachedConfig(): array
    {
        $routerCacheHandler = $this->routerConfig->getRouteCacheHandler();

        if (!$this->routerConfig->isCached() || !$routerCacheHandler->exists()) {
            throw new CacheFileDoesNotExistException('Cannot load cached config');
        }

        $routerSerializedFile = $routerCacheHandler->retrieve();

        if (!$routerSerializedFile) {
            throw new CacheFileInvalidFormatException('Cannot load cached config');
        }

        return $routerCacheHandler->deserialize($routerSerializedFile);
    }

    /**
     * @return array
     * @throws ComposerFileDoesNotExistException
     * @throws ComposerFileInvalidFormatException
     * @throws InvalidParameterAlreadyBoundException
     * @throws ReflectionException
     */
    private function loadConfig(): array
    {
        if (!file_exists($this->routerConfig->getComposerFile())) {
            throw new ComposerFileDoesNotExistException("Cannot locate the composer.json file.");
        }

        $composerJsonFile = file_get_contents($this->routerConfig->getComposerFile());

        $json = json_decode($composerJsonFile, true);

        if (empty($json['autoload']['psr-4'])) {
            throw new ComposerFileInvalidFormatException(
                "composer.json is invalid or missing psr-4 configuration."
            );
        }

        $routes = [];

        foreach ($json['autoload']['psr-4'] as $namespace => $path) {
            if ($this->isInExcludedDirectory($path)) {
                continue;
            }
            $routes = array_merge(
                $routes,
                $this->buildRoutes(
                    $this->routerConfig->getRootDirectory() . DIRECTORY_SEPARATOR . $path,
                    $namespace
                )
            );
        }

        $this->routerConfig->getRouteCacheHandler()->serializeAndStore(
            $routes,
            !$this->getRouterConfig()->isCached()
        );

        return $routes;
    }

    /**
     * @param string $path
     * @return bool
     */
    private function isInExcludedDirectory(string $path): bool
    {
        $rootDirectory = $this->routerConfig->getRootDirectory() . DIRECTORY_SEPARATOR;
        if (str_starts_with($path, $rootDirectory)) {
            $path = substr($path, strlen($rootDirectory));
        }

        foreach($this->routerConfig->getExcludedDirectories() as $directory) {
            if (str_starts_with($path, $directory)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $directory
     * @param array $results
     * @return array
     */
    private function getDirectoryContents(string $directory, array &$results = array()): array
    {
        $files = scandir($directory);

        foreach ($files as $value) {
            $path = realpath($directory . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path) && str_ends_with($path, '.php')) {
                $results[] = $path;
            } elseif ('.' !== $value && '..' !== $value && is_dir($path)) {
                $this->getDirectoryContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

    /**
     * @param string $root
     * @param string $namespace
     * @return array
     * @throws InvalidParameterAlreadyBoundException
     * @throws ReflectionException
     */
    private function buildRoutes(string $root, string $namespace): array
    {
        $structure = [];
        $files = $this->getDirectoryContents($root);

        $routes = [];

        foreach ($files as $file) {
            if (is_dir($file) || $this->isInExcludedDirectory($file)) {
                continue;
            }

            require_once $file;

            $pathParts = explode(DIRECTORY_SEPARATOR, $file);
            $filename = end($pathParts);
            $class = str_replace('.php', '', $filename);
            $replaced = str_replace([$root, $class . '.php'], '', $file);
            $classNamespace = $namespace . str_replace(DIRECTORY_SEPARATOR, '\\', $replaced);

            if (!class_exists($classNamespace . $class)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($classNamespace . $class);
            $attributes = $reflectionClass->getAttributes();

            if (empty($attributes)) {
                continue;
            }

            $controller = new Controller($class, $classNamespace);

            $this->logger->debug($class . ' is being scanned ');

            if (!empty($this->routerConfig->getGlobalPreFlights())) {
                $controller->setPreFlight($this->routerConfig->getGlobalPreFlights());
            }

            if (!empty($this->routerConfig->getGlobalPostFlights())) {
                $controller->setPostFlight($this->routerConfig->getGlobalPostFlights());
            }

            foreach ($attributes as $attribute) {
                $instance = $attribute->newInstance();
                if ($instance instanceof AttributeContract) {
                    $instance->applyToController($controller);
                }
            }

            if (!$controller->isController()) {
                continue;
            }

            $methods = $reflectionClass->getMethods();
            foreach ($methods as $method) {
                try {
                    $routes [] = $this->buildMethodRoutes($controller, $method);
                } catch (NoMethodRouteFoundException $noMethodRouteFoundException) {
                    $this->logger->debug($noMethodRouteFoundException->getMessage());
                }
            }

        }
        return $routes;
    }

    /**
     * @param Controller $controller
     * @param ReflectionMethod $method
     * @return Route
     * @throws Exceptions\Attributes\InvalidParameterAlreadyBoundException
     * @throws NoMethodRouteFoundException
     */
    private function buildMethodRoutes(Controller $controller, ReflectionMethod $method): Route
    {
        if (0 === stripos('__', $method->getName())) {
            throw new NoMethodRouteFoundException("Not applying routes to magic methods");
        }

        $attributes = $method->getAttributes();

        if (empty($attributes)) {
            throw new NoMethodRouteFoundException("No attributes defined");
        }

        $route = new Route(
            $controller->getNamespace() . $controller->getClass(),
            $method->getName(),
            $controller->getPath()
        );

        if ($controller->getAccepts()) {
            $route->setAccepts($controller->getAccepts());
        }

        if (!empty($controller->getPreFlight())) {
            $route->setPreFlight($controller->getPreFlight());
        }

        if (!empty($controller->getPostFlight())) {
            $route->setPostFlight($controller->getPostFlight());
        }

        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof AttributeContract) {
                $instance->applyToRoute($route);
            }
        }

        $parameters = $method->getParameters();

        foreach ($parameters as $parameter) {
            try {
                $this->buildMethodParameters($route, $parameter);
            } catch (MethodParameterNotMappedException $e) {
                $this->logger->debug($e->getMessage());
            }
        }

        return $route;
    }

    /**
     * @param Route $route
     * @param ReflectionParameter $parameter
     * @return void
     * @throws InvalidParameterAlreadyBoundException
     * @throws MethodParameterNotMappedException
     */
    private function buildMethodParameters(Route $route, ReflectionParameter $parameter): void
    {
        $attributes = $parameter->getAttributes();

        if (empty($attributes)) {
            throw new MethodParameterNotMappedException("No attributes against method parameter");
        }

        $validators = [];
        $parameterInstance = null;

        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof Parameter) {
                $instance->setType($parameter->getType()->getName());
                $parameterInstance = $instance;
            } elseif ($instance instanceof ValidatorInterface) {
                $validators [] = $instance;
            }
        }

        foreach ($validators as $validator) {
            $parameterInstance->addValidator($validator);
        }

        if (empty($parameterInstance)) {
            throw new MethodParameterNotMappedException("No valid Parameter attribute against method parameter");
        }

        $route->addParameter(
            $parameter->getName(),
            $parameterInstance
        );
    }
}
