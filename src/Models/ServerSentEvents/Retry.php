<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\ServerSentEvents;

class Retry implements ServerSentEvent
{
    public function __construct(
        public int $intervalInMilliseconds
    ) {
    }

    public function dispatch(): string
    {
        echo "retry: {$this->intervalInMilliseconds}";
    }
}
