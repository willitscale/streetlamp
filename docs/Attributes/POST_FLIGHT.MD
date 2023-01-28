# PostFlight

The post-flight attribute allows you to define a class to be executed after the route returned the response object.
Post-flight attributes must implement the Flight interface and for the post-flight the `post` method will be executed.
The `post` method takes the `ResponseBuilder` as a parameter which can be modified prior to the build execution of the `ResponseBuilder`.

## Definition
```php
#[PostFlight(string $flight)]
```

- `flight` must be a full class name including namespace.

## Targets

- `Attribute::TARGET_CLASS`
- `Attribute::TARGET_METHOD`

## Example

Code:
```php
#[RouteController]
#[Path('/postflight')]
#[PostFlight(MyApp\Logger::class)]
class MyClass {
    
    #[Method(HttpMethod::GET)]
    #[PostFlight(MyApp\XmlMapper::class)]
    public function postFlightExample(): ResponseBuilder
    {
        return (new ResponseBuilder())
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT);
    }
}
```

cURL:
```shell
curl --request GET \ 
    --location 'http://localhost/postflight'
```
