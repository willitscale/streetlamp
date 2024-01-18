<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp;

use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonArray;
use willitscale\Streetlamp\Attributes\Parameter\BodyParameter;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Attributes\Route\Method;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;

#[RouteController]
#[Path('/json')]
class JsonTestController
{
    #[Method(HttpMethod::POST)]
    #[Path('/array')]
    public function mapJsonArray(
        #[BodyParameter([], __DIR__ . DIRECTORY_SEPARATOR . 'json.test.dat')]
        #[JsonArray(DataType::class)] array $dataTypes
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setData($dataTypes)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }
}
