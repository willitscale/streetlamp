# Configuration

The Streetlamp has a flexible configuration which allows you to override most parts of the router.
At the very top level you can do most of this with the `RouterConfig` or even easier with the `RouterConfigBuilder`.
By default, you can initialise the `Router` without any config and for all intensive purposes will work just fine.

## Config File

Without specifying any config the `RouterBuilder` will try and initialise a config from the config file `router.conf.json`.
Don't worry if this file does not exist as it will be ignored if it does not.

### router.conf.json

The structure of the config file follows these constraints:

```javascript
{
    "rootDirectory":       ?string,   // The root directory of your application
    "composerFile":        ?string,   // The location of your application's composer file
    "cached":              ?bool,     // Caching stores and retrieves the application's route mapping
    "rethrowExceptions":   ?bool,     // The router has built in error handling, but you can optionally 
                                      // throw the exceptions and handle them yourself
    "excludedDirectories": ?string[], // Paths to not allow the route builder to scan
    "request":             ?string    // Full namespace to a class that implements `RequestInterface`
    "routeCacheHandler":   ?string,   // Full namespace to a class that extends `RouteCacheHandler`
    "globalPreFlights":    ?string[], // Array of namespaces and classes that implement `Flight` to 
                                      // be applied to all routes as pre-flights
    "globalPostFlights":   ?string[]  // Array of namespaces and classes that implement `Flight` to 
                                      // be applied to all routes as post-flights
}
```

The config can also be set pragmatically too using the `RouterConfigBuilder` like so:

### RouterConfigBuilder

```php
        $routerConfig = (new RouterConfigBuilder())
            ->setRootDirectory(__DIR__)
            ->setComposerFile(__DIR__ . DIRECTORY_SEPARATOR . 'composer.json')
            ->setCached(false)
            ->setRethrowExceptions(true)
            ->setExcludedDirectories(['tests'])
            ->setRequest(new CommandLineRequest('test', '/', 'text/html'))
            ->setRouteCacheHandler(new NullRouteCacheHandler())
            ->setGlobalPreFlights([Logger::class])
            ->setGlobalPostFlights([PerformanceAnalyser::class])
            ->build();

        $routeBuilder = new RouteBuilder(
            $routerConfig
        );
```

## Config Settings

### Root Directory

The root directory needs to be set to the root path of your application.
Setting this value is important if you have a nested entry point to your application.
It's used as the base level to scan your application from.

### Composer File

The composer file needs to be that of your application.
This file is used deciding what directories to scan as it will iterate over the `autoload->psr-4` sources to extract routes from.

### Cached

Setting cached to true will instruct the router to use the cached version of the route map where applicable.
If there is no valid route map cache then it will build and write it.
Caching is powered by the cache handler and is something you can customise.

### Re-Throw Exceptions

Streetlamp by default catches and handles the expected internal errors, but if you would like to handle these yourself simply set this to `true`.

### Excluded Directories

Excluded directories is an array of directories you don't want the `RouteBuilder` to scan when building routes.
It's important to note that these are paths appended onto the end of the root directory so you need to specify relative paths e.g. `tests`.

### Request

Request is an override to the request object being used.
By default the `HttpRequest` is used, but if this does not fulfil your needs or you want to do a command line version then this will need to be replaced.
There is a built in `CommandLineRequest` available, but you can define your own as long as it implements the `RequestInterface`.

### Cache Handler

The cache handler allows you to override how the route maps are cached by the `RouteBuilder`.
By default this uses the `FileRouteCacheHandler` which writes the cache to your system's temporary directory.
There is a built in `NullRouteCacheHandler` if you want to completely disable all caching.
Similar to the `Request` this can be completely customised by implementing your own as long as it extends the `RouteCacheHandler` abstract class.

### Global Pre-Flights

Global pre-flights are pre-flight actions to be executed on every single route.
All pre-flight classes defined must implement the `Flight` interface.

### Global Post-Flights

Global post-flights are post-flight actions to be executed on every single route.
All post-flight classes defined must implement the `Flight` interface.
