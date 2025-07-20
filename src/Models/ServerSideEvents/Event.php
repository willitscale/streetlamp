<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\ServerSideEvents;

class Event implements ServerSideEvent
{
    public function __construct(
        public string $event
    ) {
    }

    public function dispatch(): string
    {
        return "event: {$this->event}";
    }
}
