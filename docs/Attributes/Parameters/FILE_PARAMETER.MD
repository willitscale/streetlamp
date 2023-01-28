# FileParameter

The file parameter attribute binds a method parameter to a specified file input from the request.

## Definition
```php
#[FileParameter(readonly string|null $key, readonly ValidatorInterface[] $validators = [])]
```

- `key` referrers to the file input identifier
- `validators` is an array of classes which implement `ValidatorInterface` and are used to validate and sanitize inputs.

## Targets

- `Attribute::TARGET_PARAMETER`

## Example

Code:
```php
    #[Method(HttpMethod::POST)]
    #[Path('/file')]
    public function fileParameter(
        #[FileParameter('file')] string $file
    ): ResponseBuilder {
        return (new ResponseBuilder())
            ->setHttpStatusCode(HttpStatusCode::HTTP_ACCEPTED);
    }
```

cURL:
```shell
curl --request POST \
    --location 'http://localhost/files' \
    --header "Content-Type: multipart/form-data" \
    --form 'file=@/tmp/file'
```
