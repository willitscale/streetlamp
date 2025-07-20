<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\JsonRpc;

use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;
use willitscale\Streetlamp\Attributes\Validators\RegExpValidator;

#[JsonObject]
readonly class Request
{
    public function __construct(
        #[JsonProperty] #[RegExpValidator("/2\.0/")] private string $jsonrpc,
        #[JsonProperty] private string|int $id,
        #[JsonProperty] private string $method,
        #[JsonProperty(false)] private array|object|null $params = null
    ) {
    }

    public function getJsonRpc(): string
    {
        return $this->jsonrpc;
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParams(): array|object|null
    {
        return $this->params;
    }
}
