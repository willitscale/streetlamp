<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Exceptions;

use n3tw0rk\Streetlamp\Enums\HttpStatusCode;

class InvalidArgumentMethodException extends StreetLampRequestException
{
    public function __construct(
        string $code = "",
        string $message = ""
    ) {
        parent::__construct($code, $message, HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR);
    }
}