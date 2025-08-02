<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp\Controllers;

use Psr\Http\Message\ResponseInterface;
use willitscale\Streetlamp\Attributes\Accepts;
use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Attributes\Route\Method;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;

#[RouteController]
class MultiAcceptController
{
    #[Method(HttpMethod::GET)]
    #[Path('/ping')]
    #[Accepts(MediaType::TEXT_EVENT_STREAM)]
    #[Accepts(MediaType::APPLICATION_JSON)]
    public function ping(): ResponseInterface
    {
        return new ResponseBuilder()
            ->setData('pong')
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }
}
