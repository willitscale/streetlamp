<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Requests;

use Psr\Http\Message\UriInterface;
use willitscale\Streetlamp\Exceptions\Request\UriException;

class Uri implements UriInterface
{
    private string $scheme = '';
    private string $userInfo = '';
    private string $host = '';
    private ?int $port = null;
    private string $path = '';
    private string $query = '';
    private string $fragment = '';

    public function __construct(string $uri = '')
    {
        if (empty($uri)) {
            $this->load();
        } else {
            $this->parse($uri);
        }
    }

    // Not sure if I'm the biggest fan of this
    private function load(): void
    {
        $this->scheme = (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']))
            ? $_SERVER['HTTP_X_FORWARDED_PROTO']
            : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');
        $this->host = $_SERVER['HTTP_X_FORWARDED_HOST']
            ?? $_SERVER['HTTP_HOST']
            ?? ($_SERVER['SERVER_NAME'] ?? '');
        if (false !== stripos($this->host, ':')) {
            $this->host = explode(':', $this->host, 2)[0];
        }
        $this->port = isset($_SERVER['HTTP_X_FORWARDED_PORT'])
            ? (int)$_SERVER['HTTP_X_FORWARDED_PORT']
            : (isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : null);
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $queryString = $_SERVER['QUERY_STRING'] ?? '';
        if ($queryString !== '' && str_contains($requestUri, '?' . $queryString)) {
            $this->path = substr($requestUri, 0, -strlen('?' . $queryString));
        } else {
            $this->path = $requestUri;
        }
        $this->query = $queryString;
        $this->fragment = '';
        $this->userInfo = '';
    }

    // Not sure if I'm the biggest fan of this either
    private function parse(string $uri): void
    {
        $parts = parse_url($uri);
        if ($parts === false) {
            throw new UriException('RI001', 'Invalid URI');
        }
        $this->scheme = $parts['scheme'] ?? '';
        $this->userInfo = $parts['user'] ?? '';
        if (isset($parts['pass'])) {
            $this->userInfo .= ':' . $parts['pass'];
        }
        $this->host = $parts['host'] ?? '';
        $this->port = $parts['port'] ?? null;
        $this->path = $parts['path'] ?? '';
        $this->query = $parts['query'] ?? '';
        $this->fragment = $parts['fragment'] ?? '';
    }

    public function getScheme(): string
    {
        return strtolower($this->scheme);
    }

    public function getAuthority(): string
    {
        $authority = $this->host;

        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }
        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }
        return $authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function getHost(): string
    {
        return strtolower($this->host);
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme($scheme): UriInterface
    {
        $clone = clone $this;
        $clone->scheme = $scheme;
        return $clone;
    }

    public function withUserInfo($user, $password = null): UriInterface
    {
        $clone = clone $this;
        $clone->userInfo = $user;
        if ($password !== null) {
            $clone->userInfo .= ':' . $password;
        }
        return $clone;
    }

    public function withHost($host): UriInterface
    {
        $clone = clone $this;
        $clone->host = $host;
        return $clone;
    }

    public function withPort($port): UriInterface
    {
        $clone = clone $this;
        $clone->port = $port;
        return $clone;
    }

    public function withPath($path): UriInterface
    {
        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    public function withQuery($query): UriInterface
    {
        $clone = clone $this;
        $clone->query = $query;
        return $clone;
    }

    public function withFragment($fragment): UriInterface
    {
        $clone = clone $this;
        $clone->fragment = $fragment;
        return $clone;
    }

    public function __toString(): string
    {
        $uri = '';

        if ($this->scheme !== '') {
            $uri .= $this->scheme . ':';
        }

        if ($authority = $this->getAuthority()) {
            $uri .= '//' . $authority;
        }

        $uri .= $this->path;
        if ($this->query !== '') {
            $uri .= '?' . $this->query;
        }

        if ($this->fragment !== '') {
            $uri .= '#' . $this->fragment;
        }

        return $uri;
    }
}
