# TODO List

Welcome to the TODO list. This is just a collection of thoughts and ideas on how to improve Streetlamp that I didn't deem core to the project start.

## Cleanup
- Rework `Router::route` and `RouteBuilder::buildRoutes` as the functions are too big
- Review how the validators are loaded, I would prefer them to be loaded with DI

## Features
- Review the possibility of optional path parameters to reduce Path annotations. From the initial review seems like it would cause path collisions. 
- In data bindings review how to nest data bind objects within arrays.

## Resilience 
- More Tests
- Performance Benchmarks