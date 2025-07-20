<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\ServerSentEvents;

interface ServerSentEvent
{
    public function dispatch(): string;
}
