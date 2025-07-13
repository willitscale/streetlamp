<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\CacheHandlers;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

class NullCacheHandler implements CacheInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return null;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        return true;
    }

    public function clear(): bool
    {
        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $key => null;
        }
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        return true;
    }

    public function has(string $key): bool
    {
        return true;
    }

    public function delete(string $key): bool
    {
        return true;
    }
}
