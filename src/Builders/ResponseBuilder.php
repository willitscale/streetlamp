<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Builders;

use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use willitscale\Streetlamp\Attributes\DataBindings\DataBindingObjectInterface;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Exceptions\InvalidResponseReturnedToClientException;
use willitscale\Streetlamp\Requests\Stream;
use willitscale\Streetlamp\Responses\Response;
use willitscale\Streetlamp\Responses\ServerSentEvents;
use willitscale\Streetlamp\Responses\ServerSentEventsDispatcher;

class ResponseBuilder
{
    private HttpStatusCode $httpStatusCode;
    private MediaType|string $contentType = MediaType::TEXT_PLAIN;
    private mixed $data;
    private ?ServerSentEventsDispatcher $streamDispatcher;
    private array $headers = [];

    public function setData(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function setStreamDispatcher(ServerSentEventsDispatcher $streamDispatcher): self
    {
        $this->streamDispatcher = $streamDispatcher;
        return $this;
    }

    public function setHttpStatusCode(HttpStatusCode $httpStatusCode): self
    {
        $this->httpStatusCode = $httpStatusCode;
        return $this;
    }

    public function setContentType(MediaType|string $contentType): self
    {
        if ($contentType instanceof MediaType) {
            $contentType = $contentType->value;
        }

        $this->contentType = $contentType;
        return $this;
    }

    public function addHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function getHttpStatusCode(): HttpStatusCode
    {
        return $this->httpStatusCode;
    }

    public function getContentType(): MediaType|string
    {
        return $this->contentType;
    }

    public function build(): ResponseInterface
    {
        $stream = new Stream('php://temp', 'rw+');

        if (!empty($this->contentType)) {
            $this->addHeader('Content-Type', $this->contentType->value ?? $this->contentType);
            $isJson = ($this->contentType === MediaType::APPLICATION_JSON->value);
        }

        if ($this->contentType === MediaType::TEXT_EVENT_STREAM->value) {
            return $this->buildServerSentEventsResponse($stream);
        }

        if (isset($this->data)) {
            if (is_object($this->data)) {
                $reflectionClass = new ReflectionClass($this->data);
                $reflectionAttributes = $reflectionClass->getAttributes(JsonObject::class);

                foreach ($reflectionAttributes as $attribute) {
                    $attributeInstance = $attribute->newInstance();
                    if ($attributeInstance instanceof DataBindingObjectInterface) {
                        $this->data = $attributeInstance->getSerializable($reflectionClass, $this->data);
                    }
                }
            }

            if ($isJson) {
                $this->data = json_encode($this->data, JSON_THROW_ON_ERROR);
            }

            $stream->write((string)$this->data);

            if (!is_string($this->data) && !is_int($this->data) && !is_float($this->data) && !is_bool($this->data)) {
                throw new InvalidResponseReturnedToClientException(
                    "RB001",
                    "Response to client must be a primitive, received: " . gettype($this->data)
                );
            }
        }

        return new Response(
            $stream,
            $this->httpStatusCode->value,
            $this->headers,
            $_SERVER["SERVER_PROTOCOL"] ?? 'HTTP/1.1',
            $this->contentType instanceof MediaType ? $this->contentType->value : $this->contentType
        );
    }

    private function buildServerSentEventsResponse(Stream $stream): ResponseInterface
    {
        if (!isset($this->streamDispatcher)) {
            throw new InvalidResponseReturnedToClientException(
                "RB002",
                "ServerSentEvents response requires a ServerSentEvents instance."
            );
        }

        return new ServerSentEvents(
            $stream,
            $this->httpStatusCode->value,
            $this->headers,
            $_SERVER["SERVER_PROTOCOL"] ?? 'HTTP/1.1',
            '',
            $this->streamDispatcher
        );
    }
}
