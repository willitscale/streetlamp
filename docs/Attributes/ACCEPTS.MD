# Accepts

Specify one or more accepted media type for the route.
Only requests with the corresponding `Content-Type` will match the attributed route which can either be applied to:
- `Class` - applies this accepted media type to all routes contained within, but can be overridden at route level
- `Method` - applies this accepted media type to the route associated to the method

## Definition
```php
#[Accepts(string|MediaType $mediaType)]
```

- `$mediaType` corresponds to the incoming requested content type to be matched, a comprehensive list of media types is outlined in https://www.iana.org/assignments/media-types/media-types.xhtml.

## Targets

- `Attribute::TARGET_CLASS`
- `Attribute::TARGET_METHOD`
- `Attribute::IS_REPEATABLE`

## Example

Code:
```php
    #[Method(HttpMethod::GET)]
    #[Path('/example')]
    #[Accepts(MediaType::APPLICATION_JSON)]
    public function acceptsExample(): ResponseInterface
    {
        return new ResponseBuilder()
            ->setData('accepted')
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }
```

cURL:
```shell
curl --request POST \ 
    --location 'http://localhost/example' \
    --header 'Content-Type: application/json'
```