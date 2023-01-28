<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Exceptions;

use n3tw0rk\Streetlamp\Enums\HttpStatusCode;

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
