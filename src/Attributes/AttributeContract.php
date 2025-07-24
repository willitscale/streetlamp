<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes;

interface AttributeContract
{
    public function bind(string $key): void;
}
