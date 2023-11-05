<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Builders;

use willitscale\Streetlamp\CacheHandlers\CacheHandler;
use willitscale\Streetlamp\CacheHandlers\FileCacheHandler;
use willitscale\Streetlamp\Models\RouterConfig;
use willitscale\Streetlamp\Requests\HttpRequest;
use willitscale\Streetlamp\Requests\RequestInterface;

class RouterConfigBuilder
{
    private string $rootDirectory = RouterConfig::APPLICATION_DIRECTORY;
    private string $composerFile = RouterConfig::APPLICATION_DIRECTORY . 'composer.json';
    private bool $routeCached = false;
    private bool $rethrowExceptions = false;
    private array $excludedDirectories = ['tests'];
    private CacheHandler $routeCacheHandler;
    private CacheHandler $cacheHandler;
    private RequestInterface $request;
    private array $globalPreFlights;
    private array $globalPostFlights;

    public function setConfigFile(string $configFile): RouterConfigBuilder
    {
        if (!file_exists($configFile)) {
            return $this;
        }

        $configSource = file_get_contents($configFile);
        $config = json_decode($configSource, true);

        if ($config) {
            foreach ($config as $key => $value) {
                if (in_array($key, ['routeCacheHandler', 'request'])) {
                    if (class_exists($key)) { // TODO: Fix this as this is way too nested.
                        $this->{$key} = new $key();
                    }
                } else {
                    $this->{$key} = $value;
                }
            }
        }

        return $this;
    }

    public function setRootDirectory(string $rootDirectory): RouterConfigBuilder
    {
        $this->rootDirectory = $rootDirectory;
        return $this;
    }

    public function setComposerFile(string $composerFile): RouterConfigBuilder
    {
        $this->composerFile = $composerFile;
        return $this;
    }

    public function setRouteCached(bool $routeCached): RouterConfigBuilder
    {
        $this->routeCached = $routeCached;
        return $this;
    }

    public function setRethrowExceptions(bool $rethrowExceptions): RouterConfigBuilder
    {
        $this->rethrowExceptions = $rethrowExceptions;
        return $this;
    }

    public function setExcludedDirectories(array $excludedDirectories): RouterConfigBuilder
    {
        $this->excludedDirectories = $excludedDirectories;
        return $this;
    }

    public function setRouteCacheHandler(CacheHandler $routeCacheHandler): RouterConfigBuilder
    {
        $this->routeCacheHandler = $routeCacheHandler;
        return $this;
    }

    public function setCacheHandler($cacheHandler): RouterConfigBuilder
    {
        $this->routeCacheHandler = $cacheHandler;
        return $this;
    }

    public function setRequest(RequestInterface $request): RouterConfigBuilder
    {
        $this->request = $request;
        return $this;
    }

    public function setGlobalPreFlights(array $globalPreFlights): RouterConfigBuilder
    {
        $this->globalPreFlights = $globalPreFlights;
        return $this;
    }

    public function setGlobalPostFlights(array $globalPostFlights): RouterConfigBuilder
    {
        $this->globalPostFlights = $globalPostFlights;
        return $this;
    }

    /**
     * @return RouterConfig
     */
    public function build(): RouterConfig
    {
        return new RouterConfig(
            $this->rootDirectory,
            $this->composerFile,
            $this->routeCached,
            $this->rethrowExceptions,
            $this->excludedDirectories,
            $this->request ?? new HttpRequest(),
            $this->routeCacheHandler ?? new FileCacheHandler(),
            $this->cacheHandler ?? new FileCacheHandler(),
            $this->globalPreFlights ?? [],
            $this->globalPostFlights ?? []
        );
    }
}
