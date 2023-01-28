<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Exceptions\Parameters;

use n3tw0rk\Streetlamp\Enums\HttpStatusCode;
use n3tw0rk\Streetlamp\Exceptions\StreetLampRequestException;

class MissingRequiredPathException extends StreetLampRequestException
{
    public function __construct(
        string $code = "",
        string $message = ""
    ) {
        parent::__construct($code, $message, HttpStatusCode::HTTP_BAD_REQUEST);
    }
}
