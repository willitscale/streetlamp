<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes\DataBindings;

use ReflectionClass;
use stdClass;

interface DataBindingObjectInterface
{
    public function build(ReflectionClass $class, string $data): object;

    public function getObject(ReflectionClass $class, mixed $data): object;

    public function getSerializable(ReflectionClass $reflectionClass, object $object): mixed;
}
