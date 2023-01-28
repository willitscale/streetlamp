<?php declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequiredHeaderException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class HeaderParameter extends Parameter
{
    /**
     * @param array $pathMatches
     * @return string|int|bool|float
     * @throws MissingRequiredHeaderException
     */
    public function value(array $pathMatches): string|int|bool|float
    {
        $headerServerKey = 'HTTP_' . strtoupper($this->key);

        if (empty($_SERVER[$headerServerKey])) {
            throw new MissingRequiredHeaderException("HeaderParameter missing expected value " . $this->key);
        }

        return $_SERVER[$headerServerKey];
    }
}
