<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Requests;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

readonly class Request implements RequestInterface
{
    public function __construct(
        private string $protocolVersion,
        private string $method,
        private string $requestTarget,
        private array $headers,
        private UriInterface $uri,
        private StreamInterface $body
    ) {
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        return new self(
            $version,
            $this->method,
            $this->requestTarget,
            $this->headers,
            $this->uri,
            $this->body
        );
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return array_key_exists($name, $this->headers);
    }

    public function getHeader(string $name): array
    {
        return $this->headers[$name] ?? [];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(',', $this->getHeader($name));
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        $value = is_iterable($value) ? $value : [$value];
        $headers = array_merge($this->headers, [$name => $value]);

        return new self(
            $this->protocolVersion,
            $this->method,
            $this->requestTarget,
            $headers,
            $this->uri,
            $this->body
        );
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $headers = $this->headers;
        if (!isset($headers[$name])) {
            $headers[$name] = [];
        }
        $headers[$name][] = $value;

        return new self(
            $this->protocolVersion,
            $this->method,
            $this->requestTarget,
            $headers,
            $this->uri,
            $this->body
        );
    }

    public function withoutHeader(string $name): MessageInterface
    {
        $headers = $this->headers;
        unset($headers[$name]);

        return new self(
            $this->protocolVersion,
            $this->method,
            $this->requestTarget,
            $headers,
            $this->uri,
            $this->body
        );
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        return new self(
            $this->protocolVersion,
            $this->method,
            $this->requestTarget,
            $this->headers,
            $this->uri,
            $body
        );
    }

    public function getRequestTarget(): string
    {
        return $this->requestTarget;
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        return new self(
            $this->protocolVersion,
            $this->method,
            $requestTarget,
            $this->headers,
            $this->uri,
            $this->body
        );
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): RequestInterface
    {
        return new self(
            $this->protocolVersion,
            $method,
            $this->requestTarget,
            $this->headers,
            $this->uri,
            $this->body
        );
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        if ($preserveHost || !isset($this->headers['host'])) {
            // TODO: Update the host
        }

        return new self(
            $this->protocolVersion,
            $this->method,
            $this->requestTarget,
            $this->headers,
            $this->uri,
            $this->body
        );
    }
}
