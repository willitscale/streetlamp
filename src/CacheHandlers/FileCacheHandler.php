<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\CacheHandlers;

class FileCacheHandler extends CacheHandler
{
    public function __construct(private string|null $path = null)
    {
        $this->path = $path ?? sys_get_temp_dir() . DIRECTORY_SEPARATOR;
    }

    public function serialize(mixed $data): string
    {
        return serialize($data);
    }

    public function deserialize(string $data): mixed
    {
        return unserialize($data);
    }

    public function store(string $key, string $data, int $ttl = 0): void
    {
        file_put_contents($this->path . $key, $data);
    }

    public function retrieve(string $key): string
    {
        return file_get_contents($this->path . $key);
    }

    public function exists(string $key): bool
    {
        return file_exists($this->path . $key);
    }

    public function delete(string $key): bool
    {
        return unlink($this->path . $key);
    }
}
