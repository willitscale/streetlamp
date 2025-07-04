<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\CacheRules;

use willitscale\Streetlamp\Models\Route;

class ParameterCacheRule extends CacheRule
{
    public function __construct(
        private readonly string $rule,
        int $cacheTtl = CacheRule::ONE_HOUR
    ) {
        parent::__construct($cacheTtl);
    }

    public function getKey(Route $route, array $args = []): string
    {
        $rule = $this->rule;
        foreach ($args as $key => $value) {
            $rule = str_replace($key, $value, $rule);
        }
        return hash('sha384', parent::getKey($route, $args) . "__" . $rule);
    }
}
