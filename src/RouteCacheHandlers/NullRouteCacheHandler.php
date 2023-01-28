<?php declare(strict_types=1);

namespace willitscale\Streetlamp\RouteCacheHandlers;

class NullRouteCacheHandler extends RouteCacheHandler
{
    public function serialize(array $data): null
    {
        return null;
    }

    public function deserialize(string $data): array
    {
        return [];
    }

    public function store(string $data): void
    {
    }

    public function retrieve(): null
    {
        return null;
    }

    public function exists(): bool
    {
        return false;
    }
}
