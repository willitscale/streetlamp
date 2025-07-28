<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp\Controllers;

use Psr\Http\Message\ResponseInterface;
use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonArray;
use willitscale\Streetlamp\Attributes\Parameter\BodyParameter;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Builders\Route\Method;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\StreetlampTests\TestApp\Models\DataType;
use willitscale\StreetlampTests\TestApp\Models\NestedDataType;

#[RouteController]
#[Path('/json')]
class JsonTestController
{
    public const string DATA_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'json.test.dat';

    #[Method(HttpMethod::POST)]
    #[Path('/array')]
    public function mapJsonArray(
        #[BodyParameter(true, [], self::DATA_DIR)]
        #[JsonArray(DataType::class)] array $dataTypes
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($dataTypes)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }

    #[Method(HttpMethod::POST)]
    #[Path('/nested')]
    public function mapNestedJsonArray(
        #[BodyParameter(true, [], self::DATA_DIR)]
        NestedDataType $nestedDataType
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($nestedDataType)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }
}
