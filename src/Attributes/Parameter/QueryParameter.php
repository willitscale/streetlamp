<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequireQueryException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class QueryParameter extends Parameter
{
    /**
     * @param array $pathMatches
     * @return string|int|bool|float
     * @throws MissingRequireQueryException
     */
    public function value(array $pathMatches): string|int|bool|float
    {
        if (empty($_GET[$this->key])) {
            throw new MissingRequireQueryException("QP001", "Query string missing expected value for " . $this->key);
        }

        return $_GET[$this->key];
    }
}
