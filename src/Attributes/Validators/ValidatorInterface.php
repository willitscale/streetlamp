<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Validators;

interface ValidatorInterface
{
    public function validate(string $value): bool;
    public function sanitize(string $value): string|int|float|bool;
}
