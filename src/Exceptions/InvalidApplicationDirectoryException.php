<?php

namespace willitscale\Streetlamp\Exceptions;

use willitscale\Streetlamp\Enums\HttpStatusCode;

class InvalidApplicationDirectoryException extends StreetLampRequestException
{
    public function __construct(string $code, string $message)
    {
        parent::__construct($code, $message, HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR);
    }
}
