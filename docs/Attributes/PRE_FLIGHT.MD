# PreFlight

The pre-flight attribute allows you to define a class to be executed before the route has been initialised.
Preflight attributes must implement the `Flight` interface and for the pre-flight the `pre` method will be executed.
The `pre` method takes the `Request` as a parameter which can be modified prior to the route being executed.

## Definition
```php
#[PreFlight(string $flight)]
```

- `flight` must be a full class name including namespace.

## Targets

- `Attribute::TARGET_CLASS`
- `Attribute::TARGET_METHOD`

## Example

Code:
```php
#[RouteController]
#[Path('/preflight')]
#[PreFlight(MyApp\Authentication::class)]
class MyClass {
    
    #[Method(HttpMethod::GET)]
    #[PreFlight(MyApp\RateLimit::class)]
    public function preFlightExample(): ResponseBuilder
    {
        return (new ResponseBuilder())
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT);
    }
}
```

cURL:
```shell
curl --request GET \ 
    --location 'http://localhost/preflight'
```
