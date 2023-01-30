<?php declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequiredPostException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class PostParameter extends Parameter
{
    /**
     * @param array $pathMatches
     * @return string|int|bool|float|array
     * @throws MissingRequiredPostException
     */
    public function value(array $pathMatches): string|int|bool|float|array
    {
        if (empty($_POST[$this->key])) {
            throw new MissingRequiredPostException("PDP001", "Post missing expected value for " . $this->key);
        }

        return $_POST[$this->key];
    }
}
