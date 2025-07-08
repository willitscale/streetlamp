# QueryParameter

The query parameter attribute binds a method parameter to a specified entry from the request query string.

## Definition
```php
#[QueryParameter(readonly string|null $key, bool $required = false, readonly ValidatorInterface[] $validators = [])]
```

- `key` referrers to the query string identifier
- `required` indicates whether the parameter is mandatory or not.
- `validators` is an array of classes which implement `ValidatorInterface` and are used to validate and sanitize inputs.

## Targets

- `Attribute::TARGET_PARAMETER`

## Example

Code:
```php
    #[Method(HttpMethod::GET)]
    #[Path('/query')]
    public function queryParameter(
        #[FileParameter('age')] int $age
    ): ResponseInterface {
        return (new ResponseBuilder())
            ->setData($age)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }
```

cURL:
```shell
curl --request GET \
    --location 'http://localhost/path/123' \
    --data-urlencode 'age=30'
```
