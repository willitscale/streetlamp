<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models;

use willitscale\Streetlamp\Requests\HttpRequest;
use willitscale\Streetlamp\Requests\RequestInterface;
use willitscale\Streetlamp\RouteCacheHandlers\RouteCacheHandler;
use willitscale\Streetlamp\RouteCacheHandlers\FileRouteCacheHandler;

readonly class RouterConfig
{
    public const APPLICATION_DIRECTORY = '.' . DIRECTORY_SEPARATOR;

    /**
     * @param string $rootDirectory
     * @param string $composerFile
     * @param bool $cached
     * @param bool $rethrowExceptions
     * @param array $excludedDirectories
     * @param RequestInterface $request
     * @param RouteCacheHandler $routeCacheHandler
     * @param array $globalPreFlights
     * @param array $globalPostFlights
     */
    public function __construct(
        private string $rootDirectory = self::APPLICATION_DIRECTORY,
        private string $composerFile = self::APPLICATION_DIRECTORY . 'composer.json',
        private bool $cached = false,
        private bool $rethrowExceptions = false,
        private array $excludedDirectories = ['tests'],
        private RequestInterface $request = new HttpRequest(),
        private RouteCacheHandler $routeCacheHandler = new FileRouteCacheHandler(),
        private array $globalPreFlights = [],
        private array $globalPostFlights = []
    ) {
    }

    /**
     * @return string
     */
    public function getRootDirectory(): string
    {
        return $this->rootDirectory;
    }

    /**
     * @return string
     */
    public function getComposerFile(): string
    {
        return $this->composerFile;
    }

    /**
     * @return bool
     */
    public function isCached(): bool
    {
        return $this->cached;
    }

    /**
     * @return bool
     */
    public function isRethrowExceptions(): bool
    {
        return $this->rethrowExceptions;
    }

    /**
     * @return array
     */
    public function getExcludedDirectories(): array
    {
        return $this->excludedDirectories;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return RouteCacheHandler
     */
    public function getRouteCacheHandler(): RouteCacheHandler
    {
        return $this->routeCacheHandler;
    }

    /**
     * @return array
     */
    public function getGlobalPreFlights(): array
    {
        return $this->globalPreFlights;
    }

    /**
     * @return array
     */
    public function getGlobalPostFlights(): array
    {
        return $this->globalPostFlights;
    }
}
