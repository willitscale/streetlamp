<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Exceptions\Validators;

use n3tw0rk\Streetlamp\Enums\HttpStatusCode;
use n3tw0rk\Streetlamp\Exceptions\StreetLampRequestException;

class InvalidParameterFailedToPassFilterValidation extends StreetLampRequestException
{
    public function __construct(
        string $code = "",
        string $message = ""
    ) {
        parent::__construct($code, $message, HttpStatusCode::HTTP_BAD_REQUEST);
    }
}
