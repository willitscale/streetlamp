<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequiredFilesException;
use willitscale\Streetlamp\Models\File;
use willitscale\Streetlamp\Requests\ServerRequest;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FileParameter extends Parameter
{
    public function value(array $pathMatches, ServerRequest $request): File
    {
        $files = $request->getUploadedFiles();

        if (empty($files[$this->key])) {
            throw new MissingRequiredFilesException("FP001", "FileParameter missing expected value for " . $this->key);
        }

        $file = $files[$this->key];

        return new File(
            $file['name'] ?? '',
            $file['path'] ?? '',
            $file['type'] ?? '',
            $file['tmp_name'] ?? '',
            $file['error'] ?? UPLOAD_ERR_OK,
            $file['size'] ?? 0
        );
    }
}
