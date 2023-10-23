<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models;

use willitscale\Streetlamp\CacheHandlers\CacheHandler;
use willitscale\Streetlamp\CacheHandlers\FileCacheHandler;
use willitscale\Streetlamp\Requests\HttpRequest;
use willitscale\Streetlamp\Requests\RequestInterface;

readonly class RouterConfig
{
    public const APPLICATION_DIRECTORY = '.' . DIRECTORY_SEPARATOR;

    public function __construct(
        private string $rootDirectory = self::APPLICATION_DIRECTORY,
        private string $composerFile = self::APPLICATION_DIRECTORY . 'composer.json',
        private bool $routeCached = false,
        private bool $rethrowExceptions = false,
        private array $excludedDirectories = ['tests'],
        private RequestInterface $request = new HttpRequest(),
        private CacheHandler $routeCacheHandler = new FileCacheHandler(),
        private CacheHandler $cacheHandler = new FileCacheHandler(),
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
    public function isRouteCached(): bool
    {
        return $this->routeCached;
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
     * @return CacheHandler
     */
    public function getRouteCacheHandler(): CacheHandler
    {
        return $this->routeCacheHandler;
    }

    public function getCacheHandler(): CacheHandler
    {
        return $this->cacheHandler;
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
