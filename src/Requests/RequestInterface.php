<?php declare(strict_types=1);

namespace willitscale\Streetlamp\Requests;
interface RequestInterface
{
    public function getMethod(): string;
    public function getPath(): string;
    public function getContentType(): string;
}
