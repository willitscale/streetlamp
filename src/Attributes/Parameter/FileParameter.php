<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequiredFilesException;

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
            throw new MissingRequiredFilesException("FP001", "FileParameter missing expected value for " . $this->key);
        }

        return $_FILES[$this->key];
    }
}
