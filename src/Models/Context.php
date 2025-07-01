<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models;

use willitscale\Streetlamp\Enums\MediaType;

abstract class Context
{
    public function __construct(
        protected string $class,
        protected string|null $path = null,
        protected string|null $accepts = null,
        protected array $middleware = []
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;
        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = str_replace('//', '/', $path);
        $this->path = preg_replace(
            '/{([a-z0-9_]+)}/i',
            '(?<\1>[^/]+)',
            $this->path
        );
        return $this;
    }

    public function appendPath(string $path): self
    {
        $this->setPath($this->path . $path);
        return $this;
    }

    public function getAccepts(): ?string
    {
        return $this->accepts;
    }

    public function setAccepts(string|MediaType $accepts): self
    {
        $this->accepts = ($accepts instanceof MediaType) ? $accepts->value : $accepts;
        return $this;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function setMiddleware(array $middleware): void
    {
        $this->middleware = $middleware;
    }

    public function addMiddleware(string $middleware): self
    {
        if (!in_array($middleware, $this->middleware)) {
            $this->middleware [] = $middleware;
        }
        return $this;
    }

    public function popMiddleware(): string
    {
        return array_shift($this->middleware);
    }
}
