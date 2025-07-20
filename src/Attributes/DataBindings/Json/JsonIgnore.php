<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\DataBindings\Json;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class JsonIgnore
{
    public function __construct(
        private bool $onlyIgnoreIfNull = false
    ) {
    }

    public function isOnlyIgnoreIfNull(): bool
    {
        return $this->onlyIgnoreIfNull;
    }
}
