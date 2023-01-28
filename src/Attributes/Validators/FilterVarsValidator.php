<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes\Validators;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER)]
readonly class FilterVarsValidator implements ValidatorInterface
{
    public function __construct(
        private int $filter,
        private int|array $options = 0
    ) {
    }

    public function validate(string $value): bool
    {
        return (false !== filter_var($value, $this->filter, $this->options));
    }

    public function sanitize(string $value): string|int|float|bool
    {
        return filter_var($value, $this->filter, $this->options);
    }
}
