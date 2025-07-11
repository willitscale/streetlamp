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
        $key = $this->getCacheKey($key);
        if ($this->has($key)) {
            unlink($this->path . $key);
            return true;
        }

        return false;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $key = $this->getCacheKey($key);
        $contents = file_get_contents($this->path . $key);

        if ($contents === false) {
            return $default;
        }

        $data = unserialize($contents);

        if (!isset($data['value'])) {
            return $default;
        }

        if (isset($data['expires_at']) && time() > $data['expires_at'] && 0 !== $data['expires_at']) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $key = $this->getCacheKey($key);

        $data = [
            'value' => $value,
            'expires_at' => $ttl ? (time() + ($ttl instanceof DateInterval ? $ttl->s : $ttl)) : 0,
        ];

        file_put_contents($this->path . $key, serialize($data));
        return true;
    }

    public function clear(): bool
    {
        return false;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            $key = $this->getCacheKey($key);
            yield $key => $this->has($key) ? $this->get($key) : $default;
        }
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        $result = true;
        foreach ($values as $key => $value) {
            $key = $this->getCacheKey($key);
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
            $key = $this->getCacheKey($key);
            if (!$this->delete($key)) {
                $result = false;
            }
        }
        return $result;
    }

    public function has(string $key): bool
    {
        $key = $this->getCacheKey($key);
        return file_exists($this->path . $key);
    }

    public function getCacheKey(string $key): string
    {
        return hash('sha1', $key);
    }
}
