# Cache

Cache the response of the route using a cache rule to define the key.

## Definition
```php
#[Cache(CacheRule $cacheRule)]
```

- `$cacheRule` - The cache rule defines how the cache key is generated.

## Targets

- `Attribute::TARGET_METHOD`

## Example

Code:
```php
    #[Method(HttpMethod::GET)]
    #[Path('/cache/{cacheId}')]
    #[Cache(new CacheRule())]
    public function simpleGetWithCacheRule(
        #[PathParameter('cacheId')] int $cacheId
    ): ResponseInterface {
        return (new ResponseBuilder())
            ->setData($cacheId)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }
```

cURL:
```shell
curl --request POST \ 
    --location 'http://localhost/cache/123' \
    --header 'Content-Type: application/json'
```
