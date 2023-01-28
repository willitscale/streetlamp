<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes\Parameter;

use Attribute;
use n3tw0rk\Streetlamp\Exceptions\Parameters\MissingRequiredFilesException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FileParameter extends Parameter
{
    /**
     * @param array $pathMatches
     * @return string|int|bool|float
     * @throws MissingRequiredFilesException
     */
    public function value(array $pathMatches): string|int|bool|float
    {
        if (empty($_FILES[$this->key])) {
            throw new MissingRequiredFilesException("FileParameter missing expected value for " . $this->key);
        }

        return $_FILES[$this->key];
    }
}
