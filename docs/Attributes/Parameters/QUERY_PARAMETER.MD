# QueryParameter

The query parameter attribute binds a method parameter to a specified entry from the request query string.

## Definition
```php
#[QueryParameter(readonly string|null $key, readonly ValidatorInterface[] $validators = [])]
```

- `key` referrers to the query string identifier
- `validators` is an array of classes which implement `ValidatorInterface` and are used to validate and sanitize inputs.

## Targets

- `Attribute::TARGET_PARAMETER`

## Example

Code:
```php
    #[Method(HttpMethod::GET)]
    #[Path('/query')]
    public function queryParameter(
        #[QueryParameter('age')] int $age
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setData($age)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }
```

cURL:
```shell
curl --request GET \
    --location 'http://localhost/query' \
    --data-urlencode 'age=30'
```
