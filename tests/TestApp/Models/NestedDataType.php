<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp\Models;

use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonArray;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class NestedDataType
{
    public function __construct(
        #[JsonArray(DataType::class, true)] private array $dataTypes,
        #[JsonProperty(true)] private array $strings
    ) {
    }

    public function getDataTypes(): array
    {
        return $this->dataTypes;
    }

    public function getStrings(): array
    {
        return $this->strings;
    }
}
