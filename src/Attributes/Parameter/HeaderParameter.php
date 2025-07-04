<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequiredHeaderException;
use willitscale\Streetlamp\Requests\ServerRequest;

#[Attribute(Attribute::TARGET_PARAMETER)]
class HeaderParameter extends Parameter
{
    public function value(array $pathMatches, ServerRequest $request): string|int|bool|float
    {
        $header = $request->getHeaderLine($this->key);
        if (empty($header)) {
            throw new MissingRequiredHeaderException("HP001", "HeaderParameter missing expected value " . $this->key);
        }

        return $header;
    }
}
