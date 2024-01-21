# TODO List

Welcome to the TODO list. This is just a collection of thoughts and ideas on how to improve Streetlamp that I didn't deem core to the project start.

## Document
- Document Docker Setup
- Document Apache/Nginx with rewrite rules
- Compatibility with JSONAPI/OPENAPI

## Cleanup
- Rework `Router::route` and `RouteBuilder::buildRoutes` as the functions are too big
- Review how the validators are loaded, I would prefer them to be loaded with DI
- Make sure all paths are using `realpath`

## Features
- Review the possibility of optional path parameters to reduce Path annotations. From the initial review seems like it would cause path collisions.
- The ability to print out the current paths of the application.
- A way to easily clear the route cache.
- The new caching mechanism should support multiple caches, future iterations of the caching will offer multi cache support.

## Resilience 
- More Tests
- Performance Benchmarks