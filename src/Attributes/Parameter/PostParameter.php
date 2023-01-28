<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes\Parameter;

use Attribute;
use n3tw0rk\Streetlamp\Exceptions\Parameters\MissingRequiredPostException;

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
            throw new MissingRequiredPostException("Post missing expected value for " . $this->key);
        }

        return $_POST[$this->key];
    }
}
