<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp\Validators;

use Attribute;
use willitscale\Streetlamp\Attributes\Validators\ValidatorInterface;
use willitscale\StreetlampTests\TestApp\Models\DataType;

#[Attribute]
class DataValidator implements ValidatorInterface
{
    public function validate(mixed $value): bool
    {
        $value = json_decode($value);

        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $object) {
            if (!isset($object->name) || !isset($object->age)) {
                return false;
            }
        }

        return true;
    }

    public function sanitize(mixed $value): mixed
    {
        $value = json_decode($value);

        foreach ($value as &$object) {
            $object = new DataType($object->name, $object->age);
        }

        return $value;
    }
}
