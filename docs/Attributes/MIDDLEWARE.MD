# Middleware

The Middleware attribute allows you to define a class to be executed before and/or after a route is processed. Middleware must implement the `MiddlewareInterface` as defined in [PSR-15](https://www.php-fig.org/psr/psr-15/).

Middleware can be used for tasks such as logging, authentication, response transformation, and more. Each middleware class receives a request and a handler, and can modify the request, response, or perform actions before or after the route handler is executed.

## Definition
```php
#[Middleware(string $middleware)]
```
- `middleware` must be a fully qualified class name including the namespace.

## Targets
- `Attribute::TARGET_CLASS`
- `Attribute::TARGET_METHOD`
- `Attribute::IS_REPEATABLE`

## Example

Code:
```php
#[RouteController]
#[Path('/middleware')]
#[Middleware(MyApp\Logger::class)]
class MyClass {
    #[Method(HttpMethod::GET)]
    #[Middleware(MyApp\XmlMapper::class)]
    public function middlewareExample(): ResponseInterface
    {
        return new ResponseBuilder()
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT)
            ->build();
    }
}
```

Middleware Example:
```php
namespace MyApp;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

readonly class Logger implements MiddlewareInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function process(
        ServerRequestInterface $request, 
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $this->logger->info('Logging preflight event');
        $response = $handler->handle($request);
        $this->logger->info('Logging postflight event');
        return $response;
    }
}
```

cURL:
```shell
curl --request GET \
    --location 'http://localhost/middleware'
```

## Notes
- Middleware classes must implement `Psr\Http\Server\MiddlewareInterface`.
- Middleware can be applied at both the class and method level. Method-level middleware is executed after class-level middleware.
- The legacy `PostFlight` attribute is no longer supported. Use `Middleware` for all pre- and post-processing needs.
