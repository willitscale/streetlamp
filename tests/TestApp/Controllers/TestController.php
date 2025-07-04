<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp\Controllers;

use Psr\Http\Message\ResponseInterface;
use willitscale\Streetlamp\Attributes\Accepts;
use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\Parameter\HeaderParameter;
use willitscale\Streetlamp\Attributes\Parameter\PathParameter;
use willitscale\Streetlamp\Attributes\Parameter\PostParameter;
use willitscale\Streetlamp\Attributes\Parameter\QueryParameter;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Attributes\Route\Method;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;

#[RouteController]
#[Path('/')]
class TestController
{
    #[Method(HttpMethod::GET)]
    public function simpleGet(): ResponseInterface
    {
        return new ResponseBuilder()
            ->setData('test')
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }

    #[Method(HttpMethod::GET)]
    #[Path('/json')]
    #[Accepts(MediaType::APPLICATION_JSON)]
    public function simpleGetThatAcceptsJsonOnly(): ResponseInterface
    {
        return new ResponseBuilder()
            ->setData(['test'])
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }

    #[Method(HttpMethod::POST)]
    public function simplePost(
        #[PostParameter('test')] string $test
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($test)
            ->setHttpStatusCode(HttpStatusCode::HTTP_CREATED)
            ->build();
    }

    #[Method(HttpMethod::PUT)]
    #[Path('/{test}')]
    public function simplePut(
        #[PathParameter('test')] string $test
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($test)
            ->setHttpStatusCode(HttpStatusCode::HTTP_ACCEPTED)
            ->build();
    }

    #[Method(HttpMethod::DELETE)]
    public function simpleDelete(#[QueryParameter('test')] int $test): ResponseInterface
    {
        return new ResponseBuilder()
            ->setData($test)
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT)
            ->build();
    }

    #[Method(HttpMethod::PATCH)]
    public function simplePatch(#[HeaderParameter('test')] string $test): ResponseInterface
    {
        return new ResponseBuilder()
            ->setData($test)
            ->setHttpStatusCode(HttpStatusCode::HTTP_ACCEPTED)
            ->build();
    }
}
