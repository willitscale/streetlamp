# Commands

Streetlamp is accompanied by a binary application which allows you to perform basic streetlamp commands.

## Available commands

The commands available are the following formats:

### Init Docker

Init docker will scaffold a simple docker compose infrastructure for you app to build and run locally.

#### Command

```bash
vendor/bin/streetlamp init docker
```

### Routes List

Routes list simply provides you with a list of all possible routes and which classes and functions they map to.
It takes two optional parameters of:
- `APPLICATION_PATH` which is the root directory of your application.
- `COMPOSER_FILE` an alternative composer file for your application.

#### Command

```bash
vendor/bin/streetlamp routes list <APPLICATION_PATH> <COMPOSER_FILE>
```

#### Example

Run using the TestApp in the Streetlamp tests.

```bash
root@4e192099443e:/app# ./streetlamp routes list tests tests/TestApp/composer.test.json
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Method | Path                               | Accepts          | Class                                                                   | Function                               |
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| GET    | /cache/(?<cacheId>[^/]+)           |                  | willitscale\StreetlampTests\TestApp\Controllers\CacheTestController     | simpleGetWithCacheRule                 |
| GET    | /cache/parameter/(?<cacheId>[^/]+) |                  | willitscale\StreetlampTests\TestApp\Controllers\CacheTestController     | simpleGetWithParameterCacheRule        |
| POST   | /json/array                        |                  | willitscale\StreetlampTests\TestApp\Controllers\JsonTestController      | mapJsonArray                           |
| POST   | /json/nested                       |                  | willitscale\StreetlampTests\TestApp\Controllers\JsonTestController      | mapNestedJsonArray                     |
| GET    | /                                  |                  | willitscale\StreetlampTests\TestApp\Controllers\TestController          | simpleGet                              |
| GET    | /json                              | application/json | willitscale\StreetlampTests\TestApp\Controllers\TestController          | simpleGetThatAcceptsJsonOnly           |
| POST   | /                                  |                  | willitscale\StreetlampTests\TestApp\Controllers\TestController          | simplePost                             |
| PUT    | /(?<test>[^/]+)                    |                  | willitscale\StreetlampTests\TestApp\Controllers\TestController          | simplePut                              |
| DELETE | /                                  |                  | willitscale\StreetlampTests\TestApp\Controllers\TestController          | simpleDelete                           |
| PATCH  | /                                  |                  | willitscale\StreetlampTests\TestApp\Controllers\TestController          | simplePatch                            |
| GET    | /validator/(?<validatorId>[^/]+)   |                  | willitscale\StreetlampTests\TestApp\Controllers\ValidatorTestController | simpleGetWithPathParameterAndValidator |
| POST   | /validator/validation              |                  | willitscale\StreetlampTests\TestApp\Controllers\ValidatorTestController | validateSingleInput                    |
| POST   | /validator/validations             |                  | willitscale\StreetlampTests\TestApp\Controllers\ValidatorTestController | validateMultipleInputs                 |
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
```

### Routes Cache Clear

A way to clear the routes cache which may persist between code changes.
It takes two optional parameters of:
- `APPLICATION_PATH` which is the root directory of your application.
- `COMPOSER_FILE` an alternative composer file for your application.

#### Command

```bash
vendor/bin/streetlamp routes cache clear <OPTIONAL_PATH> <OPTIONAL_COMPOSER_FILE>
```

