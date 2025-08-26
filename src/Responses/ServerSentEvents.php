<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Responses;

use Psr\Http\Message\StreamInterface;
use willitscale\Streetlamp\Models\ServerSentEvents\ServerSentEvent;

class ServerSentEvents extends Response implements StreamResponse
{
    public function __construct(
        StreamInterface $body,
        int $statusCode = 200,
        array $headers = [],
        string $protocolVersion = '1.1',
        string $reasonPhrase = '',
        private readonly ?ServerSentEventsDispatcher $dispatcher = null
    ) {
        $headers = array_merge($headers, [
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'no-cache'
        ]);

        parent::__construct($body, $statusCode, $headers, $protocolVersion, $reasonPhrase);
    }

    public function start(): void
    {
        set_time_limit(0);
        while ($this->dispatcher->isRunning()) {
            foreach ($this->dispatcher->dispatch() as $event) {
                if (!$event instanceof ServerSentEvent) {
                    continue;
                }
                echo $event->dispatch(), PHP_EOL, PHP_EOL;
            }

            if (connection_aborted()) {
                break;
            }

            if (ob_get_contents()) {
                ob_end_flush();
            }

            flush();
            $this->dispatcher->delay();
        }
    }
}
