<?php declare(strict_types=1);

namespace TestApp;

use n3tw0rk\Streetlamp\Attributes\Accepts;
use n3tw0rk\Streetlamp\Attributes\Controller\RouteController;
use n3tw0rk\Streetlamp\Attributes\Parameter\HeaderParameter;
use n3tw0rk\Streetlamp\Attributes\Parameter\PathParameter;
use n3tw0rk\Streetlamp\Attributes\Parameter\PostParameter;
use n3tw0rk\Streetlamp\Attributes\Parameter\QueryParameter;
use n3tw0rk\Streetlamp\Attributes\Path;
use n3tw0rk\Streetlamp\Attributes\Route\Method;
use n3tw0rk\Streetlamp\Attributes\Validators\IntValidator;
use n3tw0rk\Streetlamp\Builders\ResponseBuilder;
use n3tw0rk\Streetlamp\Enums\HttpMethod;
use n3tw0rk\Streetlamp\Enums\HttpStatusCode;
use n3tw0rk\Streetlamp\Enums\MediaType;
use n3tw0rk\Streetlamp\Requests\RequestInterface;

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
