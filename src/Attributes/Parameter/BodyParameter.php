<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequireBodyException;
use willitscale\Streetlamp\Requests\ServerRequest;

#[Attribute(Attribute::TARGET_PARAMETER)]
class BodyParameter extends Parameter
{
    public function __construct(
        bool $required = false,
        array $validators = []
    ) {
        parent::__construct(null, $required, $validators);
    }

    public function value(array $pathMatches, ServerRequest $request): string|int|bool|float|array
    {
        $stream = $request->getBody();
        $value = $stream->getContents();
        $stream->rewind();

        if (empty($value)) {
            throw new MissingRequireBodyException("BP001", "BodyParameter missing or blank");
        }

        return $value;
    }
}
