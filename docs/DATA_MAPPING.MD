# Data Mapping

Streetlamp supports object mapping with the help of the `DataBindingObjectInterface`.
You can either use pre-existing data bindings which are currently limited to JSON, or alternatively create your own.
Again, as with most parts of Streetlamp, the data mapping has fully supported extensibility to fit your needs.
As JSON is the most commonly used format for data exchange, there's an included set of libraries to allow you to map data in both directions.

## Route Definition

When defining a data binding in your route, you simply need to bind the input to that particular route.
Similarly, if you want to return an object you just need to set it as the data in the `ResponseBuilder`.

## Signatures

```php
#[JsonObject]
#[JsonIgnore]
#[JsonProperty(bool $required = true, ?string $alias = null)]
#[JsonArray(string $className, bool $required = true, ?string $alias = null)]
```

## Example

### Controller

```php
#[RouteController]
#[Path('/person')]
class Person
{
    #[Method(HttpMethod::POST)]
    #[Accepts(MediaType::APPLICATION_JSON)]
    public function createPerson(
        #[BodyParameter] PersonModel $personModel
    ): ResponseInterface
    {
        return (new ResponseBuilder())
            ->setData($personModel)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->build();
    }
}

```

### Model

```php
#[JsonObject]
class PersonModel
{
    #[JsonProperty(true)]
    #[AlphabeticValidator]
    private string $name;

    #[JsonProperty]
    private AgeModel $data;

    public function __construct(
        #[JsonProperty(true)]
        #[JsonIgnore]
        private string $email
    ) {}

}
```

As shown in the above example you can use the annotation `JsonProperty` at both member and constructor levels.

### cURL

```sh
curl  --request POST \
--location 'http://localhost/person' \
--header 'Content-Type: application/json' \
--data-raw '{
    "name": "test",
    "email": "test@example.com",
    "data": {
        "age": 60
    }
}'
```

## Mapping Arrays

In addition to mapping objects directly to variables, you can also map arrays of objects too.
This is done using the `JsonArray` attribute which can be applied to input parameters and properties of a `JsonObject`.

### Controller

```php
    #[Method(HttpMethod::POST)]
    #[Path('/array')]
    public function mapJsonArray(
        #[BodyParameter]
        #[JsonArray(DataType::class)] array $dataTypes
    ): ResponseInterface {
        return (new ResponseBuilder())
            ->setData($dataTypes)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->build();
    }
```

The `JsonArray` attribute must be used on a property that is also a `JsonObject`. Each object in the array will have validation rules applied where applicable.

### Model

```php
#[JsonObject]
readonly class NestedDataType
{
    public function __construct(
        #[JsonArray(DataType::class, true)] public array $dataTypeArray,
        #[JsonProperty(true)] public array $nativeArray
    ) {
    }
}
```

Only one of either the `JsonArray` or `JsonProperty` attributes can be applied to a property. If both are present, `JsonArray` takes priority.
