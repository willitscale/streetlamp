<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Responses;

interface ServerSentEventsDispatcher
{
    public function dispatch(): array;
    public function isRunning(): bool;
    public function delay();
}
