<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\JsonRpc;

use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonIgnore;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class Error
{
    public function __construct(
        #[JsonProperty] int $code,
        #[JsonProperty] string $message,
        #[JsonProperty(false)] #[JsonIgnore(true)] mixed $data = null
    ) {
    }
}
