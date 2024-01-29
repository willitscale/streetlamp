<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp\Controllers;

use willitscale\Streetlamp\Attributes\Cache\Cache;
use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\Parameter\PathParameter;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Attributes\Route\Method;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\CacheRules\CacheRule;
use willitscale\Streetlamp\CacheRules\ParameterCacheRule;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;

#[RouteController]
class CacheTestController
{
    #[Method(HttpMethod::GET)]
    #[Path('/cache/{cacheId}')]
    #[Cache(new CacheRule())]
    public function simpleGetWithCacheRule(
        #[PathParameter('cacheId')] int $cacheId
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setData($cacheId)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }

    #[Method(HttpMethod::GET)]
    #[Path('/cache/parameter/{cacheId}')]
    #[Cache(new ParameterCacheRule("__{cacheId}__"))]
    public function simpleGetWithParameterCacheRule(
        #[PathParameter('cacheId')] int $cacheId
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setData($cacheId)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }
}
