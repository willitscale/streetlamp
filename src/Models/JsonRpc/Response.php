<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\JsonRpc;

use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonIgnore;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class Response
{
    public function __construct(
        #[JsonProperty] private string $jsonrpc,
        #[JsonProperty] private string|int|null $id = null,
        #[JsonProperty(false)] #[JsonIgnore(true)] private array|object|null $result = null,
        #[JsonProperty(false)] #[JsonIgnore(true)] private ?Error $error = null,
    ) {
    }

    public function getJsonrpc(): string
    {
        return $this->jsonrpc;
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getResult(): array|object|null
    {
        return $this->result;
    }

    public function getError(): ?Error
    {
        return $this->error;
    }
}
