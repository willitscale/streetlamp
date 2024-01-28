# Streetlamp

[![GitHub Workflow Status (with branch)](https://img.shields.io/github/actions/workflow/status/willitscale/streetlamp/php.yml)](https://github.com/willitscale/streetlamp/actions)
[![Packagist License](https://img.shields.io/packagist/l/willitscale/streetlamp)](https://github.com/willitscale/streetlamp/blob/main/LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/willitscale/streetlamp)](https://packagist.org/packages/willitscale/streetlamp)
[![GitHub last commit](https://img.shields.io/github/last-commit/willitscale/streetlamp)](https://github.com/willitscale/streetlamp/commits/main)

## Table of Contents
- [1. Introduction](#1-introduction)
- [2. Prerequisites](#2-prerequisites)
- [3. Setup](#3-setup)

## 1. Introduction

Streetlamp is a simple routing library that allows you to quickly prototype APIs.
This library was built around the basic concepts of annotative routing, commonly found in Java libraries such as [JAX-RS](https://cxf.apache.org/docs/jax-rs.html) and [Spring](https://spring.io/).
Although the way it works is inspired from the aforementioned Java libraries, it has a slightly unique implementation more fitting the PHP language.

## 2. Prerequisites

To keep up with modern standards this library was built using PHP 8.2 and therefore will only run in said environment or greater. 
If there is enough demand I may be willing to retrofit back to PHP 8, but as it's built with attributes it can't go back any further.
Finally, this project requires composer and the [PSR-4 Autoload standard](https://www.php-fig.org/psr/psr-4/).

## 3. Setup

### 3.1. Installing the library

Simply include Streetlamp in your project with the composer command:

```sh
composer require willitscale/streetlamp
```

### 3.2. Application Wrapper

To run your application through the Streetlamp wrapper all you need to do is instantiate the `Router` class and call `route`. 
The `Router` will use a `RouteBuilder` to scan all of your namespaces in the `composer.json` (excluding test namespaces by default) and setup corresponding routes. 

Here's all the code you need to get going:

```php
<?php declare(strict_types=1);

use willitscale\Streetlamp\Router;

(new Router())->route();
```

This will use a simple out of the box configuration, if you require any customisation this can be achieved with the `RouterConfig`.
There's a comprehensive guide on configuration in the [Configuration page](docs/CONFIGURATION.MD).

### 3.3 Creating a Controller

A controller can be defined by simply giving a class the attribute of `RouteController`.

```php
<?php declare(strict_types=1);

#[RouteController]
class MyRouteClass {
}
```

Only classes with the `RouteController` attribute will be scanned for routes.

### 3.4. Creating a Route

Each public method within a `RouteController` can be annotated as a route.
There's three requirements to transform a method into a route:
- add a HTTP method attribute to the method,
- a path attribute to the method or class and
- return the `ResponseBuilder` object.

Let's say we want to create a route for the request `GET /hello HTTP/1.1`, we would need to attribute our route method with the `Get` and `Path` attributes.

Here's what that would look like in code:

```php
<?php declare(strict_types=1);

namespace Example;

use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Attributes\Route\Method;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;

#[RouteController]
class MyRouteClass
{
    #[Path('/hello')]
    #[Method(HttpMethod::GET)]
    public function simpleGet(): ResponseBuilder
    {
        return (new ResponseBuilder())
            ->setData('world')
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK);
    }
}
```

We could have also applied the `#[Path('/hello')]` to the `RouteController` and then all routes defined within the controller have that path prefixed to them so you would not need to apply a path to them individually.

## 4. Futher Reading
- [Routing Attributes](docs/ROUTING_ATTRIBUTES.MD)
- [Input Attributes](docs/INPUT_ATTRIBUTES.MD)
- [Data Mapping](docs/DATA_MAPPING.MD)
- [Validators](docs/VALIDATORS.MD)
- [Caching](docs/CACHING.MD)
- [Configuration](docs/CONFIGURATION.MD)
- [Setup](docs/SETUP.MD)
- [Testing](docs/TESTING.MD)
- [Error Codes](docs/ERROR_CODES.MD)
- [Commands](docs/COMMANDS.MD)
- [Performance](docs/PERFORMANCE.MD)
- [TODO](docs/TODO.MD)
