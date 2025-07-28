<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Builders;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;
use willitscale\Streetlamp\Attributes\AttributeClass;
use willitscale\Streetlamp\Attributes\RouteContract;
use willitscale\Streetlamp\Exceptions\CacheFileDoesNotExistException;
use willitscale\Streetlamp\Exceptions\CacheFileInvalidFormatException;
use willitscale\Streetlamp\Exceptions\ComposerFileDoesNotExistException;
use willitscale\Streetlamp\Exceptions\ComposerFileInvalidFormatException;
use willitscale\Streetlamp\Exceptions\InvalidApplicationDirectoryException;
use willitscale\Streetlamp\Exceptions\NoMethodRouteFoundException;
use willitscale\Streetlamp\Exceptions\StreetLampException;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\RouterConfig;
use willitscale\Streetlamp\Models\RouteState;
use willitscale\Streetlamp\Traits\BuildAttributes;
use willitscale\Streetlamp\Traits\BuildMethodRoutes;

class RouteBuilder
{
    use BuildMethodRoutes;
    use BuildAttributes;

    public const string ROUTER_DATA_KEY = 'router.data';

    private array $attributeClasses = [];

    public function __construct(
        private ?RouterConfig $routerConfig = null,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->routerConfig = $routerConfig ?? new RouterConfigBuilder()
            ->setConfigFile('router.conf.json')
            ->build();
    }

    public function getRouterConfig(): RouterConfig
    {
        return $this->routerConfig;
    }

    public function getRouteState(): RouteState
    {
        try {
            return $this->loadCachedConfig();
        } catch (StreetLampException $StreetLampException) {
            $this->logger->debug($StreetLampException->getMessage());
        }

        return $this->loadConfig();
    }

    private function loadCachedConfig(): RouteState
    {
        $routerCacheHandler = $this->routerConfig->getRouteCacheHandler();

        if (!$this->routerConfig->isRouteCached() || !$routerCacheHandler->has(self::ROUTER_DATA_KEY)) {
            throw new CacheFileDoesNotExistException('Cannot load cached config');
        }

        $routerFile = $routerCacheHandler->get(self::ROUTER_DATA_KEY);

        if (!$routerFile || !($routerFile instanceof RouteState)) {
            throw new CacheFileInvalidFormatException('Cannot load cached config');
        }

        return $routerFile;
    }

    public function clearCachedConfig(): bool
    {
        return $this->routerConfig->getRouteCacheHandler()->clear(self::ROUTER_DATA_KEY);
    }

    private function loadConfig(): RouteState
    {
        $composerJsonFilePath = $this->routerConfig->getComposerFile();

        if (!file_exists($composerJsonFilePath)) {
            throw new ComposerFileDoesNotExistException(
                "RTB001",
                "Cannot locate the composer file {$composerJsonFilePath}."
            );
        }

        if (is_dir($composerJsonFilePath)) {
            throw new ComposerFileDoesNotExistException(
                "RTB002",
                "Path specified {$composerJsonFilePath} is a directory not a composer file."
            );
        }


        $composerJsonFile = file_get_contents($composerJsonFilePath);

        $json = json_decode($composerJsonFile, true);

        if (empty($json['autoload']['psr-4'])) {
            throw new ComposerFileInvalidFormatException(
                "RTB003",
                "Composer file specified is invalid or missing psr-4 configuration."
            );
        }

        $routeState = new RouteState();

        foreach ($json['autoload']['psr-4'] as $namespace => $path) {
            if ($this->isInExcludedDirectory($path)) {
                continue;
            }
            $root = $this->routerConfig->getRootDirectory() . DIRECTORY_SEPARATOR . $path;
            $this->buildRoutes($routeState, $root, $namespace);
            $this->buildAttributes($routeState, $root, $namespace);
        }

        $this->routerConfig->getRouteCacheHandler()->set(self::ROUTER_DATA_KEY, $routeState);

        return $routeState;
    }

    private function isInExcludedDirectory(string $path): bool
    {
        $rootDirectory = $this->routerConfig->getRootDirectory() . DIRECTORY_SEPARATOR;
        if (str_starts_with($path, $rootDirectory)) {
            $path = substr($path, strlen($rootDirectory));
        }

        foreach ($this->routerConfig->getExcludedDirectories() as $directory) {
            if (str_starts_with($path, $directory)) {
                return true;
            }
        }

        return false;
    }

    private function getDirectoryContents(string $directory, array &$results = array()): array
    {
        if (!is_dir($directory)) {
            throw new InvalidApplicationDirectoryException(
                "RTB004",
                "{$directory} is not a valid application directory."
            );
        }

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

    private function buildRoutes(RouteState $routeState, string $root, string $namespace): void
    {
        foreach ($this->getClassesWithAttributes($root, $namespace) as $attributeClass) {
            $controller = new Controller(
                $attributeClass->getClass(),
                $attributeClass->getNamespace()
            );

            $this->logger->debug($attributeClass->getClass() . ' is being scanned ');

            if (!empty($this->routerConfig->getGlobalMiddleware())) {
                $controller->setMiddleware($this->routerConfig->getGlobalMiddleware());
            }

            foreach ($attributeClass->getAttributes() as $attribute) {
                $instance = $attribute->newInstance(); // TODO: Bind this to the container?
                if ($instance instanceof RouteContract) {
                    $instance->applyToController($controller);
                }
            }

            if (!$controller->isController()) {
                continue;
            }

            foreach ($attributeClass->getReflection()->getMethods() as $method) {
                try {
                    $this->buildMethodRoutes($attributeClass, $routeState, $controller, $method);
                } catch (NoMethodRouteFoundException $noMethodRouteFoundException) {
                    $this->logger->debug($noMethodRouteFoundException->getMessage());
                }
            }
        }
    }

    private function getClassesWithAttributes(string $root, string $namespace): array
    {
        if (!empty($this->attributeClasses[$root . $namespace])) {
            return $this->attributeClasses[$root . $namespace];
        }

        $files = $this->getDirectoryContents($root);
        $classes = [];

        foreach ($files as $file) {
            if (is_dir($file) || $this->isInExcludedDirectory($file)) {
                continue;
            }

            require_once $file;

            $pathParts = explode(DIRECTORY_SEPARATOR, $file);
            $filename = end($pathParts);
            $class = str_replace('.php', '', $filename);
            $replaced = str_replace([realpath($root), $root, $class . '.php'], '', $file);
            $classNamespace = $namespace . str_replace(DIRECTORY_SEPARATOR, "\\", $replaced);
            $classNamespace = str_replace("\\\\", "\\", $classNamespace);

            if (!class_exists($classNamespace . $class)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($classNamespace . $class);
            $attributes = $reflectionClass->getAttributes();

            if (empty($attributes)) {
                continue;
            }

            $classes [] = new AttributeClass(
                $class,
                $classNamespace,
                $reflectionClass,
                $attributes
            );
        }

        return $this->attributeClasses[$root . $namespace] = $classes;
    }
}
