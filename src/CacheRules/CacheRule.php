<?php

namespace willitscale\Streetlamp\CacheRules;

use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Models\Route;

abstract class CacheRule
{
    public function __construct(protected int $cacheTtl)
    {
    }

    abstract public function store(Route $route, ResponseBuilder $data): void;

    abstract public function get(Route $route): ResponseBuilder;

    abstract public function exists(Route $route): bool;
}
