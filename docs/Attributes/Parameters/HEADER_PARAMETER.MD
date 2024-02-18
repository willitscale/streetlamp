# HeaderParameter

The header parameter attribute binds a method parameter to a specified header from the request.

## Definition
```php
#[HeaderParameter(readonly string|null $key, readonly ValidatorInterface[] $validators = [])]
```

- `key` referrers to the header identifier
- `validators` is an array of classes which implement `ValidatorInterface` and are used to validate and sanitize inputs.

## Targets

- `Attribute::TARGET_PARAMETER`

## Example

Code:
```php
    #[Method(HttpMethod::GET)]
    #[Path('/header')]
    public function headerParameter(
        #[HeaderParameter('header')] string $header
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setHttpStatusCode(HttpStatusCode::HTTP_ACCEPTED);
    }
```

cURL:
```shell
curl --request GET \
    --location 'http://localhost/header' \
    --header "header: hello"
```
