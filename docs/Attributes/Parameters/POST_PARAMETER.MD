# PostParameter

The post parameter attribute binds a method parameter to a specified entry from the request post data.

## Definition
```php
#[PostParameter(readonly string|null $key, bool $required = false, readonly ValidatorInterface[] $validators = [])]
```

- `key` referrers to the post data identifier
- `required` indicates whether the parameter is mandatory or not.
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
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($name)
            ->setHttpStatusCode(HttpStatusCode::HTTP_CREATED)
            ->build();
    }
```

cURL:
```shell
curl --request POST \
    --location 'http://localhost/post' \
    --header "Content-Type: multipart/form-data" \
    --form 'name=test'
```
