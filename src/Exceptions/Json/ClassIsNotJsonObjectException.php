<?php

namespace willitscale\Streetlamp\Exceptions\Json;

use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Exceptions\StreetLampRequestException;

class ClassIsNotJsonObjectException extends StreetLampRequestException
{
    public function __construct(
        string $code = "",
        string $message = ""
    ) {
        parent::__construct($code, $message, HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR);
    }
}
