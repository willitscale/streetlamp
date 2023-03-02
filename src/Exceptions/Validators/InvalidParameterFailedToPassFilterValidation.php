<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Exceptions\Validators;

use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Exceptions\StreetLampRequestException;

class InvalidParameterFailedToPassFilterValidation extends StreetLampRequestException
{
    public function __construct(
        string $code = "",
        string $message = ""
    ) {
        parent::__construct($code, $message, HttpStatusCode::HTTP_BAD_REQUEST);
    }
}
