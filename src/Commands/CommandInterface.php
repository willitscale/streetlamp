<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands;

interface CommandInterface
{
    public function command(?array $arguments = []): void;
}
