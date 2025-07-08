<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Requests;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest implements ServerRequestInterface
{
    private ?string $requestTarget = null;
    private string $method;
    private UriInterface $uri;
    private StreamInterface $body;
    private array $headers;
    private string $protocolVersion;
    private array $serverParams;
    private array $cookieParams;
    private array $queryParams;
    private array $uploadedFiles;
    private $parsedBody;
    private array $attributes;

    public function __construct(
        ?string $method = null,
        ?UriInterface $uri = null,
        ?StreamInterface $body = null,
        ?array $headers = null,
        ?string $protocolVersion = '1.1',
        ?array $serverParams = null,
        ?array $cookieParams = null,
        ?array $queryParams = null,
        ?array $uploadedFiles = null,
        $parsedBody = null,
        array $attributes = []
    ) {
        $this->method = $method ?? $_SERVER['REQUEST_METHOD'];
        $this->uri = $uri ?? new Uri();
        $this->body = $body ?? new Stream();
        $this->headers = $headers ?? $this->extractHeadersFromServer();
        $this->protocolVersion = $protocolVersion ?? $_SERVER['SERVER_PROTOCOL'] ?? '1.1';
        $this->serverParams = $serverParams ?? $_SERVER;
        $this->cookieParams = $cookieParams ?? $_COOKIE;
        $this->queryParams = $queryParams ?? $_GET;
        $this->uploadedFiles = $uploadedFiles ?? $_FILES;
        $this->parsedBody = $parsedBody ?? $_POST;
        $this->attributes = $attributes;
    }

    public function extractHeadersFromServer(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (0 === stripos($name, 'HTTP_')) {
                // The only issue with this is that if a header contains underscores, they will be
                // replaced with dashes and lowercase first letter will be replaced with uppercase.
                $key = ucwords(str_replace('_', ' ', strtolower(substr($name, 5))));
                $key = str_replace(' ', '-', $key);
                $headers[$key] = [$value];
            }
        }
        return $headers;
    }

    // PSR-7 RequestInterface methods
    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }
        $target = $this->uri->getPath();

        if (empty($target)) {
            return '/';
        }

        if (!empty($this->uri->getQuery())) {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target;
    }

    public function withRequestTarget($requestTarget): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;
        return $clone;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod($method): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->method = $method;
        return $clone;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->uri = $uri;
        if (!$preserveHost) {
            $host = $uri->getHost();
            if ($host) {
                $clone->headers['Host'] = [$host];
            }
        }
        return $clone;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->protocolVersion = $version;
        return $clone;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($name): bool
    {
        return isset($this->headers[$name]);
    }

    public function getHeader($name): array
    {
        return $this->headers[$name] ?? [];
    }

    public function getHeaderLine($name): string
    {
        if (empty($this->headers[$name])) {
            return '';
        }

        if (is_array($this->headers[$name])) {
            return implode(', ', $this->headers[$name]);
        }

        return $this->headers[$name];
    }

    public function withHeader($name, $value): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->headers[$name] = is_array($value) ? $value : [$value];
        return $clone;
    }

    public function withAddedHeader($name, $value): ServerRequestInterface
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

    public function withoutHeader($name): ServerRequestInterface
    {
        $clone = clone $this;
        unset($clone->headers[$name]);
        return $clone;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    // PSR-7 ServerRequestInterface methods
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->cookieParams = $cookies;
        return $clone;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function withQueryParams(array $query): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->queryParams = $query;
        return $clone;
    }

    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;
        return $clone;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->parsedBody = $data;
        return $clone;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    public function withAttribute($name, $value): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;
        return $clone;
    }

    public function withoutAttribute($name): ServerRequestInterface
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);
        return $clone;
    }
}
