<?php

declare(strict_types=1);

namespace willitscale\Streetlamp;

use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Exceptions\InvalidContentTypeException;
use willitscale\Streetlamp\Exceptions\NoValidRouteException;
use willitscale\Streetlamp\Exceptions\StreetLampRequestException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use willitscale\Streetlamp\Models\ServerSentEvents\ServerSentEvent;
use willitscale\Streetlamp\Models\ServerSentEvents\ServerSentEventsDispatcher;
use willitscale\Streetlamp\Requests\Stream;
use willitscale\Streetlamp\Responses\Response;
use willitscale\Streetlamp\Responses\ResponseHandler;
use willitscale\Streetlamp\Responses\ServerSentEvents;

readonly class Router
{
    public function __construct(
        private RouteBuilder $routeBuilder = new RouteBuilder(),
        private Container $container = new Container(),
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->container->set(RouteBuilder::class, $routeBuilder);
        $this->container->set(Container::class, $container);
        $this->container->set(LoggerInterface::class, $logger);
    }

    public function route(): ResponseInterface
    {
        $pathMatched = false;
        $request = $this->routeBuilder->getRouterConfig()->getRequest();
        $stream = new Stream('php://temp', 'rw+');

        try {
            foreach ($this->routeBuilder->getRoutes() as $route) {
                $matches = [];

                if (!$route->matchesRoute($request, $matches)) {
                    continue;
                }

                $pathMatched = true;

                if (!$route->matchesContentType($request)) {
                    continue;
                }

                $responseHandler = $this->container->make(
                    ResponseHandler::class,
                    [
                        'route' => $route,
                        'matches' => $matches
                    ]
                );

                return $responseHandler->handle($request);
            }

            if ($pathMatched) {
                throw new InvalidContentTypeException(
                    'R002',
                    'Content type ' . $_SERVER["CONTENT_TYPE"] . ' did not match any matching path routes.'
                );
            }

            throw new NoValidRouteException('R003', 'No valid route found for ' . $request->getUri() . '.');
        } catch (StreetLampRequestException $StreetLampRequestException) {
            if ($this->routeBuilder->getRouterConfig()->isRethrowExceptions()) {
                throw $StreetLampRequestException;
            } else {
                $statusCode = $StreetLampRequestException->getHttpStatusCode();
                $this->logger->error($StreetLampRequestException->getMessage());
                $stream->write($StreetLampRequestException->getMessage());
            }
        } catch (Throwable $exception) {
            if ($this->routeBuilder->getRouterConfig()->isRethrowExceptions()) {
                throw $exception;
            } else {
                $statusCode = HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR;
                $this->logger->error($exception->getMessage());
                $stream->write($exception->getMessage());
            }
        }

        return new Response(
            $stream,
            $statusCode->value
        );
    }

    public function renderRoute(): void
    {
        $response = $this->route();
        if ($response instanceof ServerSentEvents) {
            $this->renderSseResponse();
            return;
        }

        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $value) {
            header("{$name}: {$value}");
        }
        echo $response->getBody();
    }

    public function renderSseResponse(): void
    {
        $response = $this->route();
        $serverSentEventsDispatcher = $response->getCallback();

        header("X-Accel-Buffering: no");
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");

        while (true) {
            $events = $serverSentEventsDispatcher->dispatch();
            $events = is_array($events) ? $events : [$events];

            foreach ($events as $event) {
                if (!$event instanceof ServerSentEvent) {
                    $this->logger->error('Invalid event type returned from callback: ' . gettype($event));
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
            sleep(1);
        }
    }
}
