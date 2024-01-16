<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Validators;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
readonly class IntValidator implements ValidatorInterface
{
    public function __construct(private int $max = PHP_INT_MAX, private int $min = 0)
    {
    }

    public function validate(mixed $value): bool
    {
        return (intval($value) == $value) && $value <= $this->max && $value >= $this->min;
    }

    public function sanitize(mixed $value): int
    {
        return (int) $value;
    }
}
