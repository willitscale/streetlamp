<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models\ServerSideEvents;

interface ServerSideEvent
{
    public function dispatch(): string;
}
