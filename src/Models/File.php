<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models;

use JsonSerializable;

readonly class File implements JsonSerializable
{
    public function __construct(
        private string $name,
        private string $path,
        private string $type,
        private string $tmpName,
        private int $error,
        private int $size
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTmpName(): string
    {
        return $this->tmpName;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'path' => $this->path,
            'type' => $this->type,
            'tmp_name' => $this->tmpName,
            'error' => $this->error,
            'size' => $this->size
        ];
    }
}
