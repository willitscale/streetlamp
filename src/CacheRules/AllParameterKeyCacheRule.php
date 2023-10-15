<?php

namespace willitscale\Streetlamp\CacheRules;

use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Models\Route;

class AllParameterKeyCacheRule extends CacheRule
{
    public function store(Route $route, ResponseBuilder $data): void
    {
        // TODO: Implement store() method.
    }

    public function get(Route $route): ResponseBuilder
    {
        // TODO: Implement get() method.
    }

    public function exists(Route $route): bool
    {
        // TODO: Implement exists() method.
    }
}
