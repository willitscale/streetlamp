<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequiredFilesException;
use willitscale\Streetlamp\Requests\ServerRequest;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FileParameter extends Parameter
{
    public function value(array $pathMatches, ServerRequest $request): string|int|bool|float|array
    {
        $files = $request->getUploadedFiles();

        if (empty($files[$this->key])) {
            throw new MissingRequiredFilesException("FP001", "FileParameter missing expected value for " . $this->key);
        }

        return $files[$this->key];
    }
}
