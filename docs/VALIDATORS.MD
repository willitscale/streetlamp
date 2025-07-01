
# Validators

Along side each input mechanism is corresponding support for input validators.
Validators are used to both validate and sanitize your inputs.
With Streetlamp comes a small selection of pre-built validators:

| Class                 | Sanitize                                                                                                                            | Validate                                                                                                      |
|-----------------------|-------------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------|
| `AlphabeticValidator` | Returns a string only containing alphabetic characters                                                                              | Input must only contain case insensitive alphabetic characters                                                |
| `EmailValidator`      | Returns an e-mail address string                                                                                                    | Input must be a valid e-mail address                                                                          |
| `FilterVarsValidator` | Returns a string of the sanitized input matching the [filter_var](https://www.php.net/manual/en/function.filter-var.php) definition | Input must match the defined [filter_var](https://www.php.net/manual/en/function.filter-var.php) sanitization |
| `FloatValidator`      | Returns a float value                                                                                                               | Validates input is a float within the given range                                                             |
| `IntValidator`        | Returns an int value                                                                                                                | Validates input is an int within the given range                                                              |
| `RegExpValidator`     | Returns a string matching the replace criteria if set, otherwise returns input                                                      | Validates input matches the regular expression pattern given                                                  |

Validators can be used alongside either one of the input parameters or within data bindings.

## Definition

```php
FilterVarsValidator(int $filter, int|array $options = 0);
```

## Example

```php
    #[Method(HttpMethod::GET)]
    #[Path('/validator/{validatorId}')]
    public function simpleGetWithPathParameterAndValidator(
        #[PathParameter('validatorId'] 
        #[MustBeDivisibleByThreeValidator] int $validatorId
    ): ResponseInterface {
        return (new ResponseBuilder())
            ->setData($validatorId)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }
```

## Customisation

Just like with most aspects of Streetlamp, the validators can be easily customised and implemented.
As long as the custom validator implements the `ValidatorInterface` it will work.
When implementing a custom validator if you throw any exceptions, it's essential your exceptions extend `StreetLampRequestException` to respond to the client accordingly.

Example of a custom validator:

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class MustBeDivisibleByThreeValidator implements ValidatorInterface
{
    public function validate(string $value): bool
    {
        return ((int) $value % 3) === 0;
    }

    public function sanitize(string $value): int
    {
        return ((int) $value) / 3;
    }
}
```
