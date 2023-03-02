<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Validators;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
readonly class RegExpValidator implements ValidatorInterface
{
    public function __construct(private string $pattern, private string|null $replace = null)
    {
    }

    public function validate(string $value): bool
    {
        return preg_match($this->pattern, $value);
    }

    public function sanitize(string $value): string
    {
        if (!$this->replace) {
            return $value;
        }

        return preg_replace($this->pattern, $this->replace, $value);
    }
}
