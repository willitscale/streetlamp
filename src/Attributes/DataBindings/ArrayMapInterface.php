<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\DataBindings;

interface ArrayMapInterface
{
    public function map(array $value): array;
}
