# Response Type

Specify the response type for the route. This was built specifically for the `ServerSideEvents` response type, the default mechanism is `HttpMessage`.

## Definition
```php
#[ResponseType(?string $responseType = null)]
```

- `$responseType` class and namespace of the response type which is currently only `ServerSideEvents` or `HttpMessage` (default).

## Targets

- `Attribute::TARGET_CLASS`
- `Attribute::TARGET_METHOD`

## Example

Code:
```php
    #[Method(HttpMethod::GET)]
    #[Path('/sse')]
    #[ResponseType(ServerSideEvents::class)]
    public function sse(
        ServerSideEvents $serverSideEvents
    ): void {
        $serverSideEvents
            ->setEventDelay(ServerSideEvents::SECOND_IN_MICROSECONDS)
            ->dispatch([
                new Id('123'),
                new Event('ping'),
                new Data(new TestModel("Test", "test@example.com", new AgeModel(12)))
            ]);
    }
```

cURL:
```shell
curl --request POST \ 
    --location 'http://localhost/sse'
```