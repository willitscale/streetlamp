<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\ServerSideEvents;

class Retry implements ServerSideEvent
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
