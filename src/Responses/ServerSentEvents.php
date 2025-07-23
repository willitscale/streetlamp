<?php

namespace willitscale\Streetlamp\Responses;

use Psr\Http\Message\StreamInterface;
use willitscale\Streetlamp\Models\ServerSentEvents\ServerSentEventsDispatcher;

class ServerSentEvents extends Response
{
    public function __construct(
        StreamInterface $body,
        int $statusCode = 200,
        array $headers = [],
        string $protocolVersion = '1.1',
        string $reasonPhrase = '',
        private ?ServerSentEventsDispatcher $callback = null
    ) {
        parent::__construct($body, $statusCode, $headers, $protocolVersion, $reasonPhrase);
    }

    public function getCallback(): ?ServerSentEventsDispatcher
    {
        return $this->callback;
    }
}
