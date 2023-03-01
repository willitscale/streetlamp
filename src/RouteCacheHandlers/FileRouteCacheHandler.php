<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\RouteCacheHandlers;

class FileRouteCacheHandler extends RouteCacheHandler
{
    /**
     * @param string|null $path
     */
    public function __construct(private string|null $path = null)
    {
        $this->path = $path ?? sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'router.data';
    }

    /**
     * @param array $data
     * @return string
     */
    public function serialize(array $data): string
    {
        return serialize($data);
    }

    /**
     * @param string $data
     * @return array
     */
    public function deserialize(string $data): array
    {
        return unserialize($data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function store(string $data): void
    {
        file_put_contents($this->path, $data);
    }

    /**
     * @return string
     */
    public function retrieve(): string
    {
        return file_get_contents($this->path);
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return file_exists($this->path);
    }
}
