# Method

The method attribute is used to bind HTTP request methods to a corresponding route.
The supported methods are only those documented in https://httpwg.org/specs/rfc9110.html#methods

## Definition
```php
#[Method(HttpMethod $method)]
```

- `method` requires the enum `HttpMethod` which allows `GET`, `HEAD`, `POST`, `PUT`, `DELETE`, `CONNECT`, `OPTIONS`, `TRACE` and `PATCH` methods.

## Targets

- `Attribute::TARGET_METHOD`

## Example

Code:
```php
    #[Method(HttpMethod::GET)]
    #[Path('/method')]
    public function preflightExample(): ResponseBuilder
    {
        return (new ResponseBuilder())
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }
```

cURL:
```shell
curl --request GET \ 
    --location 'http://localhost/method'
```
