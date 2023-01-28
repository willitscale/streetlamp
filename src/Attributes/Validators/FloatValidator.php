<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes\Validators;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER)]
readonly class FloatValidator implements ValidatorInterface
{
    public function __construct(private float $max = PHP_FLOAT_MAX, private float $min = 0) {}

    public function validate(string $value): bool
    {
        return (floatval($value) == $value) && $value <= $this->max && $value >= $this->min;
    }

    public function sanitize(string $value): float
    {
        return (float) $value;
    }
}
