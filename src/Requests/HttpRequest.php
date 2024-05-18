<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Requests;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class HttpRequest implements RequestInterface
{
    private array $headers = [];

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getProtocolVersion(): string
    {
        // TODO: Implement getProtocolVersion() method.
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        // TODO: Implement withProtocolVersion() method.
    }

    public function getHeaders(): array
    {
        $this->headers = [];
        // TODO: This should return a two dimensional array
        foreach ($_SERVER as $key => $value) {
            if  (str_starts_with($key, 'HTTP_')) {
                $this->headers[str_replace('HTTP_','', $key)] = $value;
            }
        }
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
        return implode(',', $this->headers[$name]) ?? '';
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        // TODO: Implement withHeader() method.
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        // TODO: Implement withAddedHeader() method.
    }

    public function withoutHeader(string $name): MessageInterface
    {
        // TODO: Implement withoutHeader() method.
    }

    public function getBody(): StreamInterface
    {
        // TODO: Implement getBody() method.
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        // TODO: Implement withBody() method.
    }

    public function getRequestTarget(): string
    {
        // TODO: Implement getRequestTarget() method.
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        // TODO: Implement withRequestTarget() method.
    }

    public function withMethod(string $method): RequestInterface
    {
        // TODO: Implement withMethod() method.
    }

    public function getUri(): UriInterface
    {
        // TODO: Implement getUri() method.
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        // TODO: Implement withUri() method.
    }
}
