<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\DataBindings;

use ReflectionClass;

interface DataBindingObjectInterface
{
    public function build(ReflectionClass $reflectionClass, string $data): object;

    public function getObject(ReflectionClass $reflectionClass, mixed $data): object;

    public function getSerializable(ReflectionClass $reflectionClass, object $object): mixed;
}
