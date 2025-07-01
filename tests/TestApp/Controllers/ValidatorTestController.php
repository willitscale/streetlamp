<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp\Controllers;

use Psr\Http\Message\ResponseInterface;
use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\Parameter\BodyParameter;
use willitscale\Streetlamp\Attributes\Parameter\PathParameter;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Attributes\Route\Method;
use willitscale\Streetlamp\Attributes\Validators\IntValidator;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\StreetlampTests\TestApp\Models\DataType;
use willitscale\StreetlampTests\TestApp\Validators\DataValidator;

#[RouteController]
class ValidatorTestController
{
    public const string DATA_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'test.dat';

    #[Method(HttpMethod::GET)]
    #[Path('/validator/{validatorId}')]
    public function simpleGetWithPathParameterAndValidator(
        #[PathParameter('validatorId')] #[IntValidator(100)] int $validatorId
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($validatorId)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }


    #[Method(HttpMethod::POST)]
    #[Path('/validator/validation')]
    public function validateSingleInput(
        #[BodyParameter(true, [], self::DATA_DIR)] DataType $dataType
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($dataType)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }

    #[Method(HttpMethod::POST)]
    #[Path('/validator/validations')]
    public function validateMultipleInputs(
        #[BodyParameter(true, [new DataValidator()], self::DATA_DIR)] array $dataTypes
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($dataTypes)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }
}
