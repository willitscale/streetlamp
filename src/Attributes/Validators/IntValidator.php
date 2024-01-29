<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Validators;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
readonly class IntValidator implements ValidatorInterface
{
    public function __construct(
        private int $min = 0,
        private int $max = PHP_INT_MAX
    ) {
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
