<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\ServerSentEvents;

class Id implements ServerSentEvent
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
