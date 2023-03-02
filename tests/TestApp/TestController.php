<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp;

use willitscale\Streetlamp\Attributes\Accepts;
use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\Parameter\HeaderParameter;
use willitscale\Streetlamp\Attributes\Parameter\PathParameter;
use willitscale\Streetlamp\Attributes\Parameter\PostParameter;
use willitscale\Streetlamp\Attributes\Parameter\QueryParameter;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Attributes\Route\Method;
use willitscale\Streetlamp\Attributes\Validators\IntValidator;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Requests\RequestInterface;

#[RouteController]
#[Path('/')]
class TestController
{
    #[Method(HttpMethod::GET)]
    public function simpleGet(): ResponseBuilder
    {
        return (new ResponseBuilder())
            ->setData('test')
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }

    #[Method(HttpMethod::GET)]
    #[Path('/json')]
    #[Accepts(MediaType::APPLICATION_JSON)]
    public function simpleGetThatAcceptsJsonOnly(): ResponseBuilder
    {
        return (new ResponseBuilder())
            ->setData(['test'])
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }

    #[Method(HttpMethod::POST)]
    public function simplePost(
        RequestInterface $request,
        #[PostParameter('test')] string $test
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setData($test)
            ->setHttpStatusCode(HttpStatusCode::HTTP_CREATED);
    }

    #[Method(HttpMethod::PUT)]
    #[Path('/{test}')]
    public function simplePut(
        RequestInterface $request,
        #[PathParameter('test')] string $test
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setData($test)
            ->setHttpStatusCode(HttpStatusCode::HTTP_ACCEPTED);
    }

    #[Method(HttpMethod::DELETE)]
    public function simpleDelete(#[QueryParameter('test')] int $test): ResponseBuilder
    {
        return (new ResponseBuilder())
            ->setData($test)
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT);
    }

    #[Method(HttpMethod::PATCH)]
    public function simplePatch(#[HeaderParameter('test')] string $test): ResponseBuilder
    {
        return (new ResponseBuilder())
            ->setData($test)
            ->setHttpStatusCode(HttpStatusCode::HTTP_ACCEPTED);
    }

    #[Method(HttpMethod::GET)]
    #[Path('/validator/{validatorId}')]
    public function simpleGetWithPathParameterAndValidator(
        #[PathParameter('validatorId')] #[IntValidator(100)] int $validatorId
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setData($validatorId)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }
}
