<?php

namespace willitscale\Streetlamp\Attributes\Cache;

use Attribute;
use willitscale\Streetlamp\Attributes\AttributeContract;
use willitscale\Streetlamp\CacheRules\CacheRule;
use willitscale\Streetlamp\Exceptions\Attributes\InvalidAttributeContextException;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class Cache implements AttributeContract
{
    public function __construct(
        private CacheRule $cacheRule
    ) {
    }

    public function applyToController(Controller $controller): void
    {
        throw new InvalidAttributeContextException("CH001", "Cannot bind the cache at controller level");
    }

    public function applyToRoute(Route $route): void
    {
        $route->setCacheRule($this->cacheRule);
    }
}
