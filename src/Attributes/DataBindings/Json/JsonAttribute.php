<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\DataBindings\Json;

use ReflectionProperty;

interface JsonAttribute
{
    public function buildProperty(object $instance, ReflectionProperty $property, mixed $jsonValue): void;
    public function getAlias(): ?string;
}
