<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Validators;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
readonly class FloatValidator implements ValidatorInterface
{
    public function __construct(
        private float $min = 0,
        private float $max = PHP_FLOAT_MAX
    ) {
    }

    public function validate(mixed $value): bool
    {
        return (floatval($value) == $value) && $value <= $this->max && $value >= $this->min;
    }

    public function sanitize(mixed $value): float
    {
        return (float) $value;
    }
}
