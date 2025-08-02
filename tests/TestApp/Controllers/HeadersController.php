<?php

namespace willitscale\StreetlampTests\TestApp\Controllers;

use Psr\Http\Message\ResponseInterface;
use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\Parameter\HeaderParameter;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Attributes\Route\Method;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;

#[RouteController]
#[Path('/headers')]
class HeadersController
{
    #[Path('/lower-case')]
    #[Method(HttpMethod::GET)]
    public function headersLowerCase(
        #[HeaderParameter('lower-case', true)] string $header,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($header)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }

    #[Path('/upper-case')]
    #[Method(HttpMethod::GET)]
    public function headersUpperCase(
        #[HeaderParameter('UPPER-CASE', true)] string $header,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($header)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }

    #[Path('/mixed-case')]
    #[Method(HttpMethod::GET)]
    public function headersMixedCase(
        #[HeaderParameter('mIxEd-CaSe', true)] string $header,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($header)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }

    #[Path('/camel-case')]
    #[Method(HttpMethod::GET)]
    public function headersCamelCase(
        #[HeaderParameter('Camel-Case', true)] string $header,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($header)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }
}
