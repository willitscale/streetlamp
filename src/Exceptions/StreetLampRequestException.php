<?php declare(strict_types=1);

namespace willitscale\Streetlamp\Exceptions;

use willitscale\Streetlamp\Enums\HttpStatusCode;

class StreetLampRequestException extends StreetLampException
{
    public function __construct(
        string $code,
        string $message,
        private readonly HttpStatusCode $httpStatusCode
    ) {
        parent::__construct($code . '::' . $message);
    }

    /**
     * @return HttpStatusCode
     */
    public function getHttpStatusCode(): HttpStatusCode
    {
        return $this->httpStatusCode;
    }
}
