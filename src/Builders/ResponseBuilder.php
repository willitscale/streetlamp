<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Builders;

use JsonSerializable;
use n3tw0rk\Streetlamp\Attributes\DataBindings\DataBindingObjectInterface;
use n3tw0rk\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use n3tw0rk\Streetlamp\Enums\HttpStatusCode;
use n3tw0rk\Streetlamp\Enums\MediaType;
use n3tw0rk\Streetlamp\Exceptions\InvalidResponseReturnedToClientException;
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
            foreach($this->headers as $key => $value) {
                header($key . ': ' . $value);
            }
        }

        if (isset($this->data)) {

            if ( is_object($this->data)) {
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

            if (!is_string($this->data)) {
                throw new InvalidResponseReturnedToClientException("RB001", "Response to client must be a string");
            }

            if ($return) {
                return (string) $this->data;
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
