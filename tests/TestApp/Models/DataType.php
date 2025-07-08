<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp\Models;

use JsonSerializable;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class DataType implements JsonSerializable
{
    public function __construct(
        #[JsonProperty(true)] private string $name,
        #[JsonProperty(true)] private int $age
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return (object)get_object_vars($this);
    }
}
