<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\ResponseTypes;

use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use willitscale\Streetlamp\Models\Route;
use willitscale\Streetlamp\Models\ServerSentEvents\ServerSentEvent;
use willitscale\Streetlamp\Requests\Stream;
use willitscale\Streetlamp\Responses\Response;

class ServerSentEvents implements ResponseTypeInterface
{
    public const int SECOND_IN_MICROSECONDS = 1000000;

    private int $eventDelayMicroseconds = 0;
    private bool $connectionOpen = true;

    public function __construct(
        private readonly Container $container,
    ) {
    }

    public function execute(Route $route, ServerRequestInterface $request, array $args): ResponseInterface
    {
        $requestArgument = [
            'request' => $request,
            'serverSentEvents' => $this,
        ];

        $application = $this->container->make(
            $route->getClass(),
            $requestArgument
        );

        header("X-Accel-Buffering: no");
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");

        while ($this->connectionOpen) {
            $this->container->call(
                [
                    $application,
                    $route->getFunction()
                ],
                array_merge($requestArgument, $args)
            );

            if (connection_aborted()) {
                break;
            }

            if (self::SECOND_IN_MICROSECONDS > $this->eventDelayMicroseconds) {
                sleep($this->eventDelayMicroseconds / self::SECOND_IN_MICROSECONDS);
            } else {
                usleep($this->eventDelayMicroseconds);
            }
        }

        return new Response(new Stream('php://temp', 'r+'));
    }

    public function setEventDelay(int $microseconds): self
    {
        $this->eventDelayMicroseconds = $microseconds;
        return $this;
    }

    public function dispatch(array $events): self
    {
        foreach ($events as $event) {
            if (!$event instanceof ServerSentEvent) {
                throw new \InvalidArgumentException(
                    'All events must implement the ServerSentEvent interface.'
                );
            }

            echo $event->dispatch(), PHP_EOL, PHP_EOL;
        }

        if (ob_get_contents()) {
            ob_end_flush();
        }

        flush();

        return $this;
    }

    public function close(): self
    {
        $this->connectionOpen = false;
        return $this;
    }
}
