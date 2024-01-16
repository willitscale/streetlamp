<?php

namespace willitscale\StreetlampTests\TestApp;

use JsonSerializable;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
class DataType implements JsonSerializable
{
    public function __construct(
        #[JsonProperty(true)] private string $name,
        #[JsonProperty(true)] private int    $age
    )
    {
    }

    public function jsonSerialize(): mixed
    {
        return (object)get_object_vars($this);
    }
}
