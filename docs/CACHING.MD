# Caching

Streetlamp has support for route response caching which can be used to cache responses before they are returned to the client.
Doing so can cut the overhead of processing each request, but all requests still have the middleware events applied.

Caching is currently limited to just one type of cache (specified in the configuration) which may be subject to change in future releases.
Out of the box the `FileCacheHandler` is the default caching mechanism, but like the rest of Streetlamp can be overridden as long as it implements the [PSR-16 Simple Cache](https://www.php-fig.org/psr/psr-16/) interface.

The Cache attribute takes a parameter of a `CacheRule` which amongst other things is used to define the cache key.

For more on caching please see the [Cache Attribute](Attributes/CACHE.MD).
