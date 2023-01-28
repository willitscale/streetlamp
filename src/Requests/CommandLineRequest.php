<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Requests;

readonly class CommandLineRequest implements RequestInterface
{
    /**
     * @param string $method
     * @param string $path
     * @param string $contentType
     */
    public function __construct(
        private string $method,
        private string $path,
        private string $contentType
    ) {}

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
