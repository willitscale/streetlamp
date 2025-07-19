<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\DataBindings\Json;

use ReflectionType;

trait JsonMatchesType
{
    private function matchesType(ReflectionType $type, mixed $value): bool
    {
        return ('array' === $type->getName() && is_array($value)) ||
            ('int' === $type->getName() && is_int($value)) ||
            ('float' === $type->getName() && is_float($value)) ||
            ('bool' === $type->getName() && is_bool($value)) ||
            ('string' === $type->getName() && is_string($value)) ||
            ('null' === $type->getName() && is_null($value)) ||
            ('object' === gettype($value) &&
                ($type->getName() === get_class($value) || $type->getName() === gettype($value)));
    }
}
