<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\ServerSentEvents;

class Event implements ServerSentEvent
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
