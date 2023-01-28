<?php declare(strict_types=1);

namespace willitscale\Streetlamp\Attributes\Parameter;

use Attribute;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequireBodyException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class BodyParameter extends Parameter
{
    public function __construct(
        array $validators = [],
        private readonly string $resourceIdentifier = 'php://input'
    ) {
        parent::__construct(null, $validators);
    }

    /**
     * @param array $pathMatches
     * @return string|int|bool|float
     * @throws MissingRequireBodyException
     */
    public function value(array $pathMatches): string|int|bool|float
    {
        $streamValue = file_get_contents($this->resourceIdentifier);

        if (empty($streamValue)) {
            throw new MissingRequireBodyException("BodyParameter missing or blank");
        }

        return $streamValue;
    }
}
