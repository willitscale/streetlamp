<?php

namespace willitscale\Streetlamp\Models\ServerSentEvents;

interface ServerSentEventsDispatcher
{
    public function dispatch(): array;
}
