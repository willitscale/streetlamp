<?php

namespace willitscale\Streetlamp\Responses;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Requests\Stream;

class Response implements ResponseInterface
{
    public function __construct(
        private StreamInterface $body,
        private int $statusCode = 200,
        private array $headers = [],
        private string $protocolVersion = '1.1',
        private string $reasonPhrase = ''
    ) {
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): ResponseInterface
    {
        $clone = clone $this;
        $clone->protocolVersion = $version;
        return $clone;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }

    public function getHeader(string $name): array
    {
        return $this->headers[$name] ?? [];
    }

    public function getHeaderLine(string $name): string
    {
        return isset($this->headers[$name]) ? implode(", ", $this->headers[$name]) : '';
    }

    public function withHeader(string $name, mixed $value): ResponseInterface
    {
        $clone = clone $this;
        $clone->headers[$name] = is_array($value) ? $value : [$value];
        return $clone;
    }

    public function withAddedHeader(string $name, mixed $value): ResponseInterface
    {
        $clone = clone $this;
        $values = is_array($value) ? $value : [$value];
        if (isset($clone->headers[$name])) {
            $clone->headers[$name] = array_merge($clone->headers[$name], $values);
        } else {
            $clone->headers[$name] = $values;
        }
        return $clone;
    }

    public function withoutHeader(string $name): ResponseInterface
    {
        $clone = clone $this;
        unset($clone->headers[$name]);
        return $clone;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(mixed $body): ResponseInterface
    {
        $stream = $body;
        if (!$body instanceof StreamInterface) {
            $stream = new Stream('php://temp', 'rw+');
            if (is_resource($body)) {
                stream_copy_to_stream($body, $stream->detach());
            } elseif (is_scalar($body) || (is_object($body) && method_exists($body, '__toString'))) {
                $stream->write((string)$body);
                $stream->rewind();
            } elseif (is_array($body) || is_object($body)) {
                $stream->write(json_encode($body));
                $stream->rewind();
            }
        }
        $clone = clone $this;
        $clone->body = $stream;
        return $clone;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        $clone = clone $this;
        $clone->statusCode = $code;
        $clone->reasonPhrase = $reasonPhrase;
        return $clone;
    }

    public function getReasonPhrase(): string
    {
        if ($this->reasonPhrase !== '') {
            return $this->reasonPhrase;
        }

        // Move this to an enum?
        $phrases = [
            HttpStatusCode::HTTP_OK->value => 'OK',
            HttpStatusCode::HTTP_CREATED->value => 'Created',
            HttpStatusCode::HTTP_NO_CONTENT->value => 'No Content',
            HttpStatusCode::HTTP_BAD_REQUEST->value => 'Bad Request',
            HttpStatusCode::HTTP_UNAUTHORIZED->value => 'Unauthorized',
            HttpStatusCode::HTTP_FORBIDDEN->value => 'Forbidden',
            HttpStatusCode::HTTP_NOT_FOUND->value => 'Not Found',
            HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR->value => 'Internal Server Error',
        ];

        return $phrases[$this->statusCode] ?? '';
    }
}
