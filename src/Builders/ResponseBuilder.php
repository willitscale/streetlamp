<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Builders;

use Psr\Http\Message\ResponseInterface;
use willitscale\Streetlamp\Attributes\DataBindings\DataBindingObjectInterface;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Exceptions\InvalidResponseReturnedToClientException;
use ReflectionClass;
use willitscale\Streetlamp\Requests\Stream;
use willitscale\Streetlamp\Responses\Response;

class ResponseBuilder
{
    private HttpStatusCode $httpStatusCode;
    private MediaType|string $contentType = MediaType::TEXT_PLAIN;
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

    public function getHttpStatusCode(): HttpStatusCode
    {
        return $this->httpStatusCode;
    }

    public function getContentType(): MediaType|string
    {
        return $this->contentType;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function build(): ResponseInterface
    {
        $isJson = false;
        $stream = new Stream('php://temp', 'rw+');

        if (!empty($this->contentType)) {
            $this->addHeader('Content-Type', $this->contentType->value ?? $this->contentType);
            $isJson = ($this->contentType === MediaType::APPLICATION_JSON->value);
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
                $this->data = json_encode($this->data, JSON_THROW_ON_ERROR);
            }

            $stream->write((string) $this->data);

            if (!is_string($this->data) && !is_int($this->data) && !is_float($this->data) && !is_bool($this->data)) {
                throw new InvalidResponseReturnedToClientException(
                    "RB001",
                    "Response to client must be a primitive, received: " . gettype($this->data)
                );
            }
        }

        return new Response(
            $stream,
            $this->httpStatusCode->value,
            $this->headers,
            $_SERVER["SERVER_PROTOCOL"] ?? 'HTTP/1.1',
            $this->contentType instanceof MediaType ? $this->contentType->value : $this->contentType
        );
    }
}
