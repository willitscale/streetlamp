# Server Sent Events (SSE)

Streetlamp has support for SSE, which allows the server to push updates to the client in real-time.
This is useful for applications that require live updates, such a Model Context Protocol server.

## Response Type

SSE responses can be defined by ResponseBuilder, setting a content type value of `text/event-stream` or the constant enum `MediaType::TEXT_EVENT_STREAM` will result in an SSE response being returned.
Along with the content type, you will also need to provide a `ServerSentEventsDispatcher` instance, which will handle the dispatching of events to the client.

## Example

An example of a route that uses SSE to send events to a client:

```php
#[RouteController]
class MainController implements ServerSentEventsDispatcher
{
    #[Method(HttpMethod::GET)]
    #[Path('/sse')]
    public function sse(): ResponseInterface
    {
        return new ResponseBuilder()
            ->setStreamDispatcher($this)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_EVENT_STREAM)
            ->build();
    }

    public function dispatch(): array
    {
        return [
            new Id('123'),
            new Event('ping'),
            new Data(new TestModel("Test", "test@example.com", new AgeModel(12)))
        ];
    }

    public function isRunning(): bool
    {
        return true;
    }

    public function delay(): void
    {
        sleep(1);
    }
}
```

In the above example, we use the controller as the dispatcher, but this isn't necessary, you can implement the `ServerSentEventsDispatcher` interface in any class and pass it to the `ResponseBuilder` using the `setStreamDispatcher` method.
When implementing the `ServerSentEventsDispatcher` interface, you must implement the following methods:
- `dispatch()`: This method should return an array of `ServerSentEvent` objects that will be sent to the client.
- `isRunning()`: This method should return a boolean indicating whether the dispatcher is still running. If it returns false, the connection will be closed.
- `delay()`: This method should be used to delay the next event dispatch. This is useful for controlling the frequency of events sent to the client, but not required and can be blank.

## Sever Sent Event Types

- `Id`: An identifier for the event, which can be used to track the event on the client side.
- `Event`: The name of the event, which can be used to represent different types of events.
- `Data`: The data to be sent to the client, which can be an object or a string. Please note that `JsonObjects` will be converted to encoded JSON.
- `Retry`: The time in milliseconds to wait before retrying the connection if it is lost.

## Example Client

A simple client that listens for SSE events can be implemented using JavaScript:

```javascript
function sseListen() {
    const evtSource = new EventSource("http://localhost:8081/sse");
    evtSource.onmessage = function (event) {
        var dataobj = JSON.parse(event.data);
        console.log(dataobj);
    }
}
```

## SSE Server Configuration

You're most likely to encounter one of the following issues when configuring a HTTP server for Server Sent Events (SSE):
1. Long-running connections - Most servers may have a set timeout for long-running connections, which can cause the connection to be closed before the events are sent. It's recommended to set the timeout to a high value or disable it entirely for specific SSE traffic.
2. Buffering - Some servers may buffer the response, which can cause delays in sending events to the client. It's recommended to only disable SSE buffering with the `X-Accel-Buffering` response header.
3. Chunked Transfer Encoding - There may be issues with chunked transfer encoding, there's mixed opinions on this, but if you do have an issue just turn it off.
