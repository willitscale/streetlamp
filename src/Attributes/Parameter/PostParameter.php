<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequiredPostException;
use willitscale\Streetlamp\Requests\ServerRequest;

#[Attribute(Attribute::TARGET_PARAMETER)]
class PostParameter extends Parameter
{
    public function value(array $pathMatches, ServerRequest $request): string|int|bool|float|array
    {
        $params = $request->getParsedBody();
        if (empty($params[$this->key])) {
            throw new MissingRequiredPostException("PDP001", "Post missing expected value for " . $this->key);
        }

        return $params[$this->key];
    }
}
