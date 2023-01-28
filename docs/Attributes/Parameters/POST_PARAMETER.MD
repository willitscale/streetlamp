# PostParameter

The post parameter attribute binds a method parameter to a specified entry from the request post data.

## Definition
```php
#[PostParameter(readonly string|null $key, readonly ValidatorInterface[] $validators = [])]
```

- `key` referrers to the post data identifier
- `validators` is an array of classes which implement `ValidatorInterface` and are used to validate and sanitize inputs.

## Targets

- `Attribute::TARGET_PARAMETER`

## Example

Code:
```php
    #[Method(HttpMethod::POST)]
    #[Path('/post')]
    public function postParameter(
        #[FileParameter('name')] string $name
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setData($name)
            ->setHttpStatusCode(HttpStatusCode::HTTP_CREATED);
    }
```

cURL:
```shell
curl --request POST \
    --location 'http://localhost/post' \
    --header "Content-Type: multipart/form-data" \
    --form 'name=test'
```
