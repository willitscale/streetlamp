<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Validators;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
readonly class FilterVarsValidator implements ValidatorInterface
{
    public function __construct(
        private int $filter,
        private int|array $options = 0
    ) {
    }

    public function validate(mixed $value): bool
    {
        return (false !== filter_var($value, $this->filter, $this->options));
    }

    public function sanitize(mixed $value): mixed
    {
        return filter_var($value, $this->filter, $this->options);
    }
}
