<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequireQueryException;
use willitscale\Streetlamp\Requests\ServerRequest;

#[Attribute(Attribute::TARGET_PARAMETER)]
class QueryParameter extends Parameter
{
    public function value(array $pathMatches, ServerRequest $request): string|int|bool|float
    {
        $queryParams = $request->getQueryParams();

        if (empty($queryParams[$this->key])) {
            throw new MissingRequireQueryException("QP001", "Query string missing expected value for " . $this->key);
        }

        return $queryParams[$this->key];
    }
}
