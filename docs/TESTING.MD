# Testing

Streetlamp offers support for route testing with the `RouteTestCase` class.
In `RouteTestCase` there's a method called `setupRouter` which sets up the Streetlamp wrapper for testing.

## Definition

```php
public function setupRouter(
        string $method,
        string $path,
        string $contentType,
        string $rootDirectory,
        string $composerFile,
        RouteCacheHandler $routeCacheHandler = new NullRouteCacheHandler()
): Router
```

## Example 

Here's an example of a test class using the `RouteTestCase` and accessing the route `GET /test HTTP/1.1`

```php
class RouterTest extends RouteTestCase
{
    public function testSimpleRoute(): void
    {
        $router = $this->setupRouter(
            'GET',
            '/test',
            MediaType::TEXT_HTML->value,
            __DIR__,
            './composer.json'
        );
        $response = $router->route(true);
        $this->assertEquals('test', $response);
    }
}
```

Note: When `route` is called on the `Router` we pass true to it, this is because passing true will return the response.
