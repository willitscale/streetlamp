<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Exceptions;

use willitscale\Streetlamp\Enums\HttpStatusCode;

class InvalidContentTypeException extends StreetLampRequestException
{
    public function __construct(
        string $code = "",
        string $message = ""
    ) {
        parent::__construct($code, $message, HttpStatusCode::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }
}
