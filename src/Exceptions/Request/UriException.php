<?php

namespace willitscale\Streetlamp\Exceptions\Request;

use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Exceptions\StreetLampRequestException;

class UriException extends StreetLampRequestException
{
    public function __construct(
        string $code = "",
        string $message = ""
    ) {
        parent::__construct($code, $message, HttpStatusCode::HTTP_BAD_REQUEST);
    }
}
