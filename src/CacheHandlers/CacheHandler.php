<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\CacheHandlers;

abstract class CacheHandler
{
    abstract public function serialize(mixed $data): string|null;

    abstract public function deserialize(string $data): mixed;

    abstract public function store(string $key, string $data, int $ttl = 0): void;

    abstract public function retrieve(string $key): string|null;

    abstract public function exists(string $key): bool;

    public function serializeAndStore(string $key, mixed $data, bool $forceWrite = false, int $ttl = 0): void
    {
        if ($forceWrite || !$this->exists($key)) {
            $serializedData = $this->serialize($data);
            if ($serializedData) {
                $this->store($key, $serializedData, $ttl);
            }
        }
    }

    public function retrieveAndDeserialize(string $key): mixed {
        $data = $this->retrieve($key);
        if ($data) {
            return $this->deserialize($data);
        }
        return null;
    }
}
