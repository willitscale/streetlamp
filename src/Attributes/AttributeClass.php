<?php

namespace willitscale\Streetlamp\Attributes;

use ReflectionAttribute;
use ReflectionClass;

readonly class AttributeClass
{
    /**
     * @param string $class
     * @param string $namespace
     * @param ReflectionClass $reflection
     * @param ReflectionAttribute[] $attributes
     */
    public function __construct(
        private string $class,
        private string $namespace,
        private ReflectionClass $reflection,
        private array $attributes,
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getReflection(): ReflectionClass
    {
        return $this->reflection;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
