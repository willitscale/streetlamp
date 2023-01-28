# BodyParameter

The body parameter attribute binds a method parameter to the request's body.

## Definition
```php
#[BodyParameter(ValidatorInterface[] $validators = [], readonly string $resourceIdentifier = 'php://input')]
```

- `validators` is an array of classes which implement `ValidatorInterface` and are used to validate and sanitize inputs.
- `resourceIdentifier` the input stream identifier used to obtain the body request data. Defaults to `php://input`

## Targets

- `Attribute::TARGET_PARAMETER`

## Example

Code:
```php
    #[Method(HttpMethod::POST)]
    #[Path('/body')]
    #[Accepts(MediaType::APPLICATION_JSON)]
    public function bodyParameter(
        #[BodyParameter] string $jsonData
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setData($jsonData)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }
```

cURL:
```shell
curl --request \
    --location POST 'http://localhost/body' \
    --header 'Content-Type: application/json' \
    --data-raw '{
    "test": "test"
}'
```
