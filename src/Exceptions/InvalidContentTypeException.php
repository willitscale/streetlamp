<?php

namespace n3tw0rk\Streetlamp\Exceptions;

use n3tw0rk\Streetlamp\Enums\HttpStatusCode;

class InvalidContentTypeException extends StreetLampRequestException
{
    public function __construct(
        string $code = "",
        string $message = ""
    ) {
        parent::__construct($code, $message, HttpStatusCode::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }
}
