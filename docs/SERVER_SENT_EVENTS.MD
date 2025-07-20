# Server Sent Events (SSE)

Streetlamp has support for SSE, which allows the server to push updates to the client in real-time.
This is useful for applications that require live updates, such a Model Context Protocol server.

## Response Type

SSE responses are defined using the `ResponseType` attribute with the parameter of `ServerSideEvents`.
This tells the framework to not treat the response as a regular HTTP message, but rather as a stream of events.
Using an SSE response type will do most of the heavy lifting for you, such as setting the correct headers and managing the connection.
It will also provide your route with access to the ServerSentEvents object, which you can use to send events to the client.

## Example

An example of a route that uses SSE to send events to a client:

```php
    #[Method(HttpMethod::GET)]
    #[Path('/sse')]
    #[ResponseType(ServerSentEvents::class)]
    public function sse(
        ServerSentEvents $serverSentEvents
    ): void {
        $serverSentEvents
            ->setEventDelay(ServerSentEvents::SECOND_IN_MICROSECONDS)
            ->dispatch([
                new Id('123'),
                new Event('ping'),
                new Data(new TestModel("Test", "test@example.com", new AgeModel(12)))
            ]);
    }
```

The route method will be called recursively at intervals defined by the `setEventDelay` method.
You can use the `dispatch` method to send events to the client. The `dispatch` method takes an array of events, which can be any object that implements the `ServerSentEvent`.

The `ServerSentEvents` has four core functions:

### setEventDelay

Sets the delay between events in microseconds.

```php
public function setEventDelay(int $microseconds): self
```

### dispatch

Dispatches an array of `ServerSentEvent` objects to the client.

```php
public function dispatch(array $events): self
```

### close

Closes the connection to the client.

```php
public function close(): self
```

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
