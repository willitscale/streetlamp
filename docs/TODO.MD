# TODO List

Welcome to the TODO list. This is just a collection of thoughts and ideas on how to improve Streetlamp that I didn't deem core to the project start.

## Document
- Compatibility with JSONAPI/OPENAPI

## Cleanup
- Review how the validators are loaded, I would prefer them to be loaded with DI
- Make sure all paths are using `realpath`

## Features
- Review the possibility of optional path parameters to reduce Path annotations. From the initial review seems like it would cause path collisions.
- Multi-lingual

## Resilience 
- More Tests
- Performance Benchmarks