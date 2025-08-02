<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models;

use willitscale\Streetlamp\Enums\MediaType;

abstract class Context
{
    public function __construct(
        protected string $class,
        protected string|null $path = null,
        protected array $accepts = [],
        protected array $middleware = [],
        protected array $attributes = []
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

    public function getAccepts(): array
    {
        return $this->accepts;
    }

    public function addAccepts(string|MediaType $accepts): self
    {
        $this->accepts [] = ($accepts instanceof MediaType) ? $accepts->value : $accepts;
        return $this;
    }

    public function setAccepts(array $accepts): self
    {
        $this->accepts = array_map(
            fn($accept) => ($accept instanceof MediaType) ? $accept->value : $accept,
            $accepts
        );
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

    public function addAttribute(string $name, mixed $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function getAttribute(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }
}
