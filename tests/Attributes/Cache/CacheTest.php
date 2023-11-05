<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Cache;

use willitscale\Streetlamp\Attributes\Cache\Cache;
use willitscale\Streetlamp\CacheRules\CacheRule;
use willitscale\Streetlamp\Exceptions\Attributes\InvalidAttributeContextException;
use willitscale\Streetlamp\Models\Controller;
use willitscale\Streetlamp\Models\Route;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testCacheAttributeDoesNotApplyToController(): void
    {
        $this->expectException(InvalidAttributeContextException::class);
        $cacheAnnotation = new Cache(new CacheRule());
        $controller = new Controller('Test', 'Test');
        $cacheAnnotation->applyToController($controller);
    }

    public function testCacheAttributeAppliesCorrectlyToRoute(): void
    {
        $cacheAnnotation = new Cache(new CacheRule());
        $route = new Route('Test', 'Test');
        $cacheAnnotation->applyToRoute($route);
        $this->assertNotNull($route->getCacheRule());
    }
}
