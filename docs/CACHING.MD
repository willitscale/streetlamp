# Caching

Streetlamp has support for route response caching which can be used to cache responses before the are returned to the client.
Doing so can cut the overhead of processing each request, but all requests still have the pre and post flight events applied.

Caching is currently limited to just one type of cache (specified in the configuration) which may be subject to change in future releases.
Out of the box the `FileCacheHandler` is the default caching mechanism, but like the rest of streetlamp can be overridden as long as it inherits the `CacheHandler` interface.

The Cache attribute takes a parameter of a `CacheRule` which amongst other things is used to define the cache key.

For more on caching please see the [Cache Attribute](Attributes/CACHE.MD).

