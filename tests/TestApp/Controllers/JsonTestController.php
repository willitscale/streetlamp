<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp\Controllers;

use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonArray;
use willitscale\Streetlamp\Attributes\Parameter\BodyParameter;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Attributes\Route\Method;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\StreetlampTests\TestApp\Models\DataType;
use willitscale\StreetlampTests\TestApp\Models\NestedDataType;

#[RouteController]
#[Path('/json')]
class JsonTestController
{
    const DATA_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'json.test.dat';

    #[Method(HttpMethod::POST)]
    #[Path('/array')]
    public function mapJsonArray(
        #[BodyParameter([], self::DATA_DIR)]
        #[JsonArray(DataType::class)] array $dataTypes
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setData($dataTypes)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }

    #[Method(HttpMethod::POST)]
    #[Path('/nested')]
    public function mapNestedJsonArray(
        #[BodyParameter([], self::DATA_DIR)]
        NestedDataType $nestedDataType
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setData($nestedDataType)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }
}
