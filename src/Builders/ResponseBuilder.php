<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Builders;

use JsonSerializable;
use willitscale\Streetlamp\Attributes\DataBindings\DataBindingObjectInterface;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Exceptions\InvalidResponseReturnedToClientException;
use ReflectionClass;
use ReflectionException;

class ResponseBuilder implements JsonSerializable
{
    private HttpStatusCode $httpStatusCode;
    private MediaType|string $contentType;
    private mixed $data;
    private array $headers = [];

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData(mixed $data): ResponseBuilder
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param HttpStatusCode $httpStatusCode
     * @return $this
     */
    public function setHttpStatusCode(HttpStatusCode $httpStatusCode): ResponseBuilder
    {
        $this->httpStatusCode = $httpStatusCode;
        return $this;
    }

    /**
     * @param MediaType|string $contentType
     * @return $this
     */
    public function setContentType(MediaType|string $contentType): ResponseBuilder
    {
        if ($contentType instanceof MediaType) {
            $contentType = $contentType->value;
        }

        $this->contentType = $contentType;
        return $this;
    }

    public function addHeader(string $key, string $value): ResponseBuilder
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * @return HttpStatusCode
     */
    public function getHttpStatusCode(): HttpStatusCode
    {
        return $this->httpStatusCode;
    }

    /**
     * @return MediaType|string
     */
    public function getContentType(): MediaType|string
    {
        return $this->contentType;
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * @param bool $return
     * @return string|null
     * @throws InvalidResponseReturnedToClientException
     * @throws ReflectionException
     */
    public function build(bool $return = false): null|string
    {
        if (!empty($this->httpStatusCode)) {
            http_response_code($this->httpStatusCode->value);
        }

        $isJson = false;

        if (!empty($this->contentType)) {
            $this->addHeader('Content-Type', $this->contentType);
            $isJson = ($this->contentType === MediaType::APPLICATION_JSON->value);
        }

        if (!$return) {
            foreach ($this->headers as $key => $value) {
                header($key . ': ' . $value);
            }
        }

        if (isset($this->data)) {
            if (is_object($this->data)) {
                $reflectionClass = new ReflectionClass($this->data);
                $reflectionAttributes = $reflectionClass->getAttributes(JsonObject::class);

                foreach ($reflectionAttributes as $attribute) {
                    $attributeInstance = $attribute->newInstance();
                    if ($attributeInstance instanceof DataBindingObjectInterface) {
                        $this->data = $attributeInstance->getSerializable($reflectionClass, $this->data);
                    }
                }
            }

            if ($isJson) {
                $this->data = json_encode($this->data);
            }

            if (!is_string($this->data) && !is_int($this->data) && !is_float($this->data) && !is_bool($this->data)) {
                throw new InvalidResponseReturnedToClientException("RB001", "Response to client must be a primitive");
            }

            if ($return) {
                return (string)$this->data;
            }

            echo $this->data;
        }

        return null;
    }

    public function jsonSerialize(): array
    {
        return [
            'httpStatusCode' => $this->httpStatusCode->value ?? HttpStatusCode::HTTP_OK->value,
            'contentType' => $this->contentType->value ?? $this->contentType ?? '',
            'data' => $this->data
        ];
    }
}
