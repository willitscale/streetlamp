# PathParameter

The path parameter attribute binds a method parameter to a variable defined in the [Path](#path) attribute.

## Definition
```php
#[PathParameter(readonly string|null $key, bool $required = false, readonly ValidatorInterface[] $validators = [])]
```

- `key` referrers to the path variable identifier
- `required` indicates whether the parameter is mandatory or not.
- `validators` is an array of classes which implement `ValidatorInterface` and are used to validate and sanitize inputs.

## Targets

- `Attribute::TARGET_PARAMETER`

## Example

Code:
```php
    #[Method(HttpMethod::GET)]
    #[Path('/path/{paramId}')]
    public function pathParameter(
        #[FileParameter('paramId')] int $paramId
    ): ResponseInterface {
        return (new ResponseBuilder())
            ->setData($paramId)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }
```

cURL:
```shell
curl --request GET \
    --location 'http://localhost/path/123'
```
