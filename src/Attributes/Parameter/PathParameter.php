<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequiredPathException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class PathParameter extends Parameter
{
    /**
     * @param array $pathMatches
     * @return string|int|bool|float
     * @throws MissingRequiredPathException
     */
    public function value(array $pathMatches): string|int|bool|float
    {
        if (empty($pathMatches[$this->key])) {
            throw new MissingRequiredPathException("PP001", "Missing path parameter of $this->key");
        }

        return current($pathMatches[$this->key]);
    }
}
