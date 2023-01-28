<?php declare(strict_types=1);

namespace willitscale\Streetlamp\Requests;

readonly class HttpRequest implements RequestInterface
{

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getPath(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    public function getContentType(): string
    {
        return $_SERVER["CONTENT_TYPE"];
    }
}
