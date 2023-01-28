<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Requests;
interface RequestInterface
{
    public function getMethod(): string;
    public function getPath(): string;
    public function getContentType(): string;
}
