<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\CacheHandlers;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

class FileCacheHandler implements CacheInterface
{
    public function __construct(private string|null $path = null)
    {
        $this->path = $path ?? sys_get_temp_dir() . DIRECTORY_SEPARATOR;
    }

    public function delete(string $key): bool
    {
        if ($this->has($key)) {
            unlink($this->path . $key);
            return true;
        }

        return false;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return file_get_contents($this->path . $key);
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        file_put_contents($this->path . $key, $value);
    }

    public function clear(): bool
    {
        return false;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $key => $this->has($key) ? $this->get($key) : $default;
        }
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        $result = true;
        foreach ($values as $key => $value) {
            if (!$this->set($key, $value, $ttl)) {
                $result = false;
            }
        }
        return $result;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $result = true;
        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $result = false;
            }
        }
        return $result;
    }

    public function has(string $key): bool
    {
        return file_exists($this->path . $key);
    }
}
