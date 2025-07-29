# Custom Attributes

The latest addition to the Streetlamp library is the ability to use custom attributes for general purposes. 
This feature allows you to define your own attributes and use them as you see fit, enhancing the flexibility and extensibility of your application.

## Defining Custom Attributes

Obviously, you must use the `#[Attribute]` attribute to mark your class as an attribute.
Your custom attribute class must implement the `AttributeContract` interface.
The bind mechanism is called which exposes the `RouteState` and `AttributeClass` objects.
The `AttributeClass` object exposes where the attribute is in use and what referenced it.
The `bind` method is invoked by the route builder so it's advised that anything you want to define, you should store in the attributes.
This is due to the route caching as the bind method will not be called again after the initial route build if caching is enabled.

```php
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_METHOD)]
readonly class CustomAttribute implements AttributeContract
{
    public function __construct(
        private string $name,
        private ?string $description = null,
    ) {
    }

    public function bind(
        RouteState $routeState,
        AttributeClass $attributeClass,
        ?string $method = null
    ): void {
        $routeState->addAttribute([
            'name' => $this->name,
            'description' => $this->description,
            'class' => $attributeClass->getNamespace() . $attributeClass->getClass(),
            'method' => $method,
        ]);
    }
}
```

## Using Custom Attributes

Using custom attributes is straightforward. You can apply them to your methods or classes (defined by your attribute) by using the attribute syntax of `#[CustomAttribute(name:"...", description:"...")]`.

```php
class CustomAttributes
{
    #[CustomAttribute('attribute1', 'This is attribute 1')]
    public function attribute1(): void
    {
    }

    #[CustomAttribute('attribute2', 'This is attribute 2')]
    public function attribute2(): void
    {
    }
}
```

## Accessing Custom Attributes

Attributes can be accessed from anywhere by injecting the `RouteState` into your class or method.
`RouteState` provides a `getAttribute` method to retrieve the value of a custom attribute.

```php
#[RouteController]
class CustomAttributeController
{
    #[Path('/custom-attribute')]
    #[Method(HttpMethod::GET)]
    public function customAttribute(
        RouteState $routeState
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($routeState->getAttributes())
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }
}
```
