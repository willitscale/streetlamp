<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes\Validators;

interface ValidatorInterface
{
    public function validate(string $value): bool;
    public function sanitize(string $value): string|int|float|bool;
}
