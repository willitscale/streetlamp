<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Validators;

interface ValidatorInterface
{
    public function validate(mixed $value): bool;
    public function sanitize(mixed $value): mixed;
}
