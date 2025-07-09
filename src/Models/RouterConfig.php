<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models;

use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;
use willitscale\Streetlamp\CacheHandlers\FileCacheHandler;
use willitscale\Streetlamp\Requests\ServerRequest;

readonly class RouterConfig
{
    public const APPLICATION_DIRECTORY = '.' . DIRECTORY_SEPARATOR;

    public function __construct(
        private string $rootDirectory = self::APPLICATION_DIRECTORY,
        private string $composerFile = self::APPLICATION_DIRECTORY . 'composer.json',
        private bool $routeCached = false,
        private bool $rethrowExceptions = false,
        private array $excludedDirectories = ['tests'],
        private ServerRequestInterface $request = new ServerRequest(),
        private CacheInterface $routeCacheHandler = new FileCacheHandler(),
        private CacheInterface $cacheHandler = new FileCacheHandler(),
        private array $globalMiddleware = []
    ) {
    }

    public function getRootDirectory(): string
    {
        return $this->rootDirectory;
    }

    public function getComposerFile(): string
    {
        return $this->composerFile;
    }

    public function isRouteCached(): bool
    {
        return $this->routeCached;
    }

    public function isRethrowExceptions(): bool
    {
        return $this->rethrowExceptions;
    }

    public function getExcludedDirectories(): array
    {
        return $this->excludedDirectories;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function getRouteCacheHandler(): CacheInterface
    {
        return $this->routeCacheHandler;
    }

    public function getCacheHandler(): CacheInterface
    {
        return $this->cacheHandler;
    }

    public function getGlobalMiddleware(): array
    {
        return $this->globalMiddleware;
    }
}
