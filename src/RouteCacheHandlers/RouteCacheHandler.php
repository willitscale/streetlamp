<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\RouteCacheHandlers;

abstract class RouteCacheHandler
{
    abstract public function serialize(array $data): string|null;

    abstract public function deserialize(string $data): array;

    abstract public function store(string $data): void;

    abstract public function retrieve(): string|null;

    abstract public function exists(): bool;

    public function serializeAndStore(array $data, bool $forceWrite = false): void
    {
        if ($forceWrite || !$this->exists()) {
            $serializedData = $this->serialize($data);
            if ($serializedData) {
                $this->store($serializedData);
            }
        }
    }
}
