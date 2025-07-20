<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\JsonRpc;

use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class Notification
{
    public function __construct(
        #[JsonProperty] private string $jsonrpc,
        #[JsonProperty] private string $method,
        #[JsonProperty(false)] private array|object|null $params = null,
    ) {
    }
}
