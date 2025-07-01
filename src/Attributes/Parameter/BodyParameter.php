<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequireBodyException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class BodyParameter extends Parameter
{
    public function __construct(
        bool $required = false,
        array $validators = [],
        private readonly string $resourceIdentifier = 'php://input'
    ) {
        parent::__construct(null, $required, $validators);
    }

    /**
     * @param array $pathMatches
     * @return string|int|bool|float|array
     * @throws MissingRequireBodyException
     */
    public function value(array $pathMatches): string|int|bool|float|array
    {
        $streamValue = file_get_contents($this->resourceIdentifier);

        if (empty($streamValue)) {
            throw new MissingRequireBodyException("BP001", "BodyParameter missing or blank");
        }

        return $streamValue;
    }
}
