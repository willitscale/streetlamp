<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Validators;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class EmailValidator implements ValidatorInterface
{
    public function validate(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function sanitize(mixed $value): mixed
    {
        return $value;
    }
}
