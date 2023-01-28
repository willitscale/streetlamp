<?php declare(strict_types=1);

namespace willitscale\Streetlamp\Builders;

use willitscale\Streetlamp\Models\RouterConfig;
use willitscale\Streetlamp\Requests\HttpRequest;
use willitscale\Streetlamp\Requests\RequestInterface;
use willitscale\Streetlamp\RouteCacheHandlers\RouteCacheHandler;
use willitscale\Streetlamp\RouteCacheHandlers\FileRouteCacheHandler;

class RouterConfigBuilder
{
    private string $rootDirectory = RouterConfig::APPLICATION_DIRECTORY;
    private string $composerFile = RouterConfig::APPLICATION_DIRECTORY . 'composer.json';
    private bool $cached = false;
    private bool $rethrowExceptions = false;
    private array $excludedDirectories = [ 'tests' ];
    private RouteCacheHandler $routeCacheHandler;
    private RequestInterface $request;
    private array $globalPreFlights;
    private array $globalPostFlights;

    public function setConfigFile(string $configFile): RouterConfigBuilder {
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

    /**
     * @param string $rootDirectory
     * @return RouterConfigBuilder
     */
    public function setRootDirectory(string $rootDirectory): RouterConfigBuilder {
        $this->rootDirectory = $rootDirectory;
        return $this;
    }

    /**
     * @param string $composerFile
     * @return RouterConfigBuilder
     */
    public function setComposerFile(string $composerFile): RouterConfigBuilder {
        $this->composerFile = $composerFile;
        return $this;
    }

    /**
     * @param bool $cached
     * @return RouterConfigBuilder
     */
    public function setCached(bool $cached): RouterConfigBuilder {
        $this->cached = $cached;
        return $this;
    }

    /**
     * @param bool $rethrowExceptions
     * @return RouterConfigBuilder
     */
    public function setRethrowExceptions(bool $rethrowExceptions): RouterConfigBuilder {
        $this->rethrowExceptions = $rethrowExceptions;
        return $this;
    }

    /**
     * @param array $excludedDirectories
     * @return RouterConfigBuilder
     */
    public function setExcludedDirectories(array $excludedDirectories): RouterConfigBuilder {
        $this->excludedDirectories = $excludedDirectories;
        return $this;
    }

    /**
     * @param RouteCacheHandler $routeCacheHandler
     * @return $this
     */
    public function setRouteCacheHandler(RouteCacheHandler $routeCacheHandler): RouterConfigBuilder
    {
        $this->routeCacheHandler = $routeCacheHandler;
        return $this;
    }

    /**
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest(RequestInterface $request): RouterConfigBuilder
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param array $globalPreFlights
     * @return RouterConfigBuilder
     */
    public function setGlobalPreFlights(array $globalPreFlights): RouterConfigBuilder
    {
        $this->globalPreFlights = $globalPreFlights;
        return $this;
    }

    /**
     * @param array $globalPostFlights
     * @return RouterConfigBuilder
     */
    public function setGlobalPostFlights(array $globalPostFlights): RouterConfigBuilder
    {
        $this->globalPostFlights = $globalPostFlights;
        return $this;
    }

    /**
     * @return RouterConfig
     */
    public function build(): RouterConfig {
        return new RouterConfig(
            $this->rootDirectory,
            $this->composerFile,
            $this->cached,
            $this->rethrowExceptions,
            $this->excludedDirectories,
            $this->request ?? new HttpRequest(),
            $this->routeCacheHandler ?? new FileRouteCacheHandler(),
            $this->globalPreFlights ?? [],
            $this->globalPostFlights ?? []
        );
    }
}
