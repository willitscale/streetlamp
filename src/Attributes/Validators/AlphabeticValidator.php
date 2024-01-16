<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Validators;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class AlphabeticValidator implements ValidatorInterface
{
    public function validate(mixed $value): bool
    {
        return preg_match('/^[a-z]+$/i', $value);
    }

    public function sanitize(mixed $value): mixed
    {
        return $value;
    }
}
