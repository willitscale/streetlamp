# RouteController

Instruct a class to be a controller.
Controllers are the only classes which can contain routes.

## Definition
```php
#[RouteController]
```

## Targets

Allowed targets:
- `Attribute::TARGET_CLASS`

## Example

Code:
```php
#[RouteController]
class MyClass {
}
```
