<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp\Controllers;

use Psr\Http\Message\ResponseInterface;
use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\Parameter\PostParameter;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Builders\Route\Method;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;

#[RouteController]
#[Path('/parameter')]
class RequiredParameterController
{
    #[Method(HttpMethod::POST)]
    #[Path('/post/not-required')]
    public function notRequiredPostParameter(
        #[PostParameter('test', false)]
        string $test = 'default'
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($test)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }

    #[Method(HttpMethod::POST)]
    #[Path('/post/required')]
    public function requiredPostParameter(
        #[PostParameter('test', true)]
        string $test
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($test)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }
}
