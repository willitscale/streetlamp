# Configuration

The Streetlamp has a flexible configuration which allows you to override most parts of the router.
At the very top level you can do most of this with the `RouterConfig` or even easier with the `RouterConfigBuilder`.
By default, you can initialise the `Router` without any config and for all intents and purposes will work just fine.

## Config File

Without specifying any config the `RouterBuilder` will try and initialise a config from the config file `router.conf.json`.
Don't worry if this file does not exist as it will be ignored if it does not.

### router.conf.json

The structure of the config file follows these constraints:

```javascript
{
    "rootDirectory":       ?string,   // The root directory of your application
    "composerFile":        ?string,   // The location of your application's composer file
    "routeCached":         ?bool,     // Caching stores and retrieves the application's route mapping
    "rethrowExceptions":   ?bool,     // The router has built in error handling, but you can optionally 
                                      // throw the exceptions and handle them yourself
    "excludedDirectories": ?string[], // Paths to not allow the route builder to scan
    "request":             ?string    // Full namespace to a class that implements `RequestInterface`
    "routeCacheHandler":   ?string,   // Full namespace to a class that extends `CacheHandler` for routes
    "cacheHandler":        ?string,   // Full namespace to a class that extends `CacheHandler` for responses
    "globalMiddleware":    ?string[], // Array of namespaces and classes that implement `Middleware` to 
                                      // be applied to all routes
}
```

The config can also be set programmatically too using the `RouterConfigBuilder` like so:

### RouterConfigBuilder

```php
        $routerConfig = new RouterConfigBuilder()
            ->setRootDirectory(__DIR__)
            ->setComposerFile(__DIR__ . DIRECTORY_SEPARATOR . 'composer.json')
            ->setRouteCached(false)
            ->setRethrowExceptions(true)
            ->setExcludedDirectories(['tests'])
            ->setRequest(new CommandLineRequest('test', '/', 'text/html'))
            ->setRouteCacheHandler(new NullCacheHandler())
            ->setCacheHandler(new NullCacheHandler())
            ->setGlobalMiddleware([Logger::class])
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
This file is used for deciding which directories to scan as it will iterate over the `autoload->psr-4` sources to extract routes from.

### Route Cached

Setting route cached to true will instruct the router to use the cached version of the route map where applicable.
If there is no valid route map cache then it will build and write it.
Route caching is powered by the cache handler and is something you can customise.

### Re-Throw Exceptions

Streetlamp by default catches and handles the expected internal errors, but if you would like to handle these yourself simply set this to `true`.

### Excluded Directories

Excluded directories is an array of directories you do not want the `RouteBuilder` to scan when building routes.
It is important to note that these are paths appended to the root directory, so you need to specify relative paths, e.g., `tests`.

### Request

Request is an override for the request object being used.
By default, the `HttpRequest` is used, but if this does not fulfill your needs or you want to use a command line version, then this will need to be replaced.
There is a built-in `CommandLineRequest` available, but you can define your own as long as it implements the `RequestInterface`.

### Route Cache Handler

The route cache handler allows you to override how the route maps are cached by the `RouteBuilder`.
By default, this uses the `FileCacheHandler`, which writes the cache to your system's temporary directory.
There is a built-in `NullCacheHandler` if you want to completely disable all caching.
Similar to the `Request`, this can be fully customized by implementing your own as long as it extends the `RouteHandler` abstract class.

### Cache Handler

The cache handler allows you to define how responses from routes are cached.
By default, this uses the `FileCacheHandler`, which writes the cache to your system's temporary directory.
There is a built-in `NullCacheHandler` if you want to completely disable all caching.
Similar to the `Request`, this can be fully customized by implementing your own as long as it extends the `RouteHandler` abstract class.

### Global Middleware

Global middleware are actions to be executed on every single route.
All middleware classes defined must implement the `MiddlewareInterface` interface.
