<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\CacheRules;

use willitscale\Streetlamp\Models\Route;

class CacheRule
{
    public const int ONE_MINUTE = 60;
    public const int ONE_HOUR = self::ONE_MINUTE * 60;
    public const int ONE_DAY = self::ONE_HOUR * 24;
    public const int ONE_WEEK = self::ONE_DAY * 7;

    public function __construct(
        private readonly int $cacheTtl = self::ONE_HOUR
    ) {
    }

    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    public function getKey(Route $route, array $args = []): string
    {
        $accepts = is_array($route->getAccepts()) ? implode(',', $route->getAccepts()) : $route->getAccepts();
        return hash('sha384', $route->getPath() . "__" . $route->getMethod() . "__" . $accepts);
    }
}
