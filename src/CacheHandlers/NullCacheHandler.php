<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\CacheHandlers;

class NullCacheHandler extends CacheHandler
{
    public function serialize(mixed $data): null
    {
        return null;
    }

    public function deserialize(string $data): mixed
    {
        return [];
    }

    public function store(string $key, string $data, int $ttl = 0): void
    {
    }

    public function retrieve(string $key): null
    {
        return null;
    }

    public function exists(string $key): bool
    {
        return false;
    }

    public function delete(string $key): bool
    {
        return true;
    }
}
