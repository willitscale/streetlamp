# Path

The path attribute creates a mapping between the request URL and the route.
You can have multiple routes with the same path, but they must use a different HTTP method or accepted media type.
If you have one or more overlapping route paths the first route matched will take priority.
Paths can be defined in both the controller and the route.
If the path is defined as part of the controller it's prefixed to all routes defined within the controller.

## Definition
```php
#[Path(string $path)]
```

- `path` assigned to the route.
  The `path` can contain variables which can be referenced by the `PathParameter` attribute.
  Variables within the path use the curly braces notation e.g. `/example/{exampleId}` for the variable `{exampleId}`.

## Targets

- `Attribute::TARGET_CLASS`
- `Attribute::TARGET_METHOD`

## Example

Code:
```php

#[RouteController]
#[Path('/resource/{resourceId}')]
class MyClass {
    
    #[Method(HttpMethod::GET)]
    #[Path('/subresource/{subresourceId}')]
    public function pathExample(
        #[PathParameter('resourceId')] int $resourceId, 
        #[PathParameter('subresourceId')] string $subresourceId
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setData([
                'resource' => $resourceId, 
                'subresource' => $subresourceId
            ])
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }
}
```

cURL:
```shell
curl --request GET \ 
    --location 'http://localhost/resource/123/subresource/abc123'
```
