# HeaderParameter

The header parameter attribute binds a method parameter to a specified header from the request.

## Definition
```php
#[HeaderParameter(readonly string|null $key, bool $required = false, readonly ValidatorInterface[] $validators = [])]
```

- `key` referrers to the header identifier
- `required` indicates whether the parameter is mandatory or not.
- `validators` is an array of classes which implement `ValidatorInterface` and are used to validate and sanitize inputs.

## Targets

- `Attribute::TARGET_PARAMETER`

## Example

Code:
```php
    #[Method(HttpMethod::GET)]
    #[Path('/header')]
    public function headerParameter(
        #[FileParameter('header')] string $header
    ): ResponseInterface {
        return (new ResponseBuilder())
            ->setHttpStatusCode(HttpStatusCode::HTTP_ACCEPTED)
            ->build();
    }
```

cURL:
```shell
curl --request GET \
    --location 'http://localhost/header' \
    --header "header: hello"
```
