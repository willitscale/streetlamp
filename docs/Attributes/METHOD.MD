# Method

The `Method` attribute is used to bind HTTP request methods to a corresponding route.
Only the methods documented in [RFC 9110](https://httpwg.org/specs/rfc9110.html#methods) are supported.

## Definition
```php
#[Method(HttpMethod $method)]
```

- `method` requires the `HttpMethod` enum, which allows `GET`, `HEAD`, `POST`, `PUT`, `DELETE`, `CONNECT`, `OPTIONS`, `TRACE`, and `PATCH` methods.

## Targets

- `Attribute::TARGET_METHOD`

## Example

Code:
```php
    #[Method(HttpMethod::GET)]
    #[Path('/method')]
    public function method(): ResponseInterface
    {
        return (new ResponseBuilder())
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
}
```

cURL:
```shell
curl --request GET \
    --location 'http://localhost/method'
```
