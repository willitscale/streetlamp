<?php

namespace willitscale\Streetlamp\CacheRules;

use willitscale\Streetlamp\Models\Route;

class CacheRule
{
    const ONE_MINUTE = 60;
    const ONE_HOUR = self::ONE_MINUTE * 60;
    const ONE_DAY = self::ONE_HOUR * 24;
    const ONE_WEEK = self::ONE_DAY * 7;

    public function __construct(private readonly int $cacheTtl = self::ONE_HOUR)
    {
    }

    /**
     * @return int
     */
    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    public function getKey(Route $route, array $args = []): string
    {
        return hash('sha384', $route->getPath() . "__" . $route->getMethod() . "__" . $route->getAccepts());
    }
}
