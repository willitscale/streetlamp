<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models;

class Controller extends Context
{
    public function __construct(
        string $class,
        private string $namespace,
        string|null $path = '',
        string|null $accepts = null,
        private bool $isController = false,
        array $middleware = [],
        array $attributes = []
    ) {
        parent::__construct($class, $path, $accepts, $middleware, $attributes);
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @return bool
     */
    public function isController(): bool
    {
        return $this->isController;
    }

    /**
     * @param bool $isController
     */
    public function setIsController(bool $isController): void
    {
        $this->isController = $isController;
    }
}
