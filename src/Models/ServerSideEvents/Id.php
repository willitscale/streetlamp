<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\ServerSideEvents;

class Id implements ServerSideEvent
{
    public function __construct(
        public string|int $id
    ) {
    }

    public function dispatch(): string
    {
        return "id: {$this->id}";
    }
}
