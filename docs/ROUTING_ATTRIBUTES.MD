# Routing Attributes

Routing attributes are used by the `RouteBuilder` to build routes.
There's a number of different attribute variations you can use between classes and methods.

The following table lists the routing attributes and where they can be used, more detailed descriptions and examples are below.

| Attribute                                         | Type         | Description                            |
|---------------------------------------------------|--------------|----------------------------------------|
| [RouteController](Attributes/ROUTE_CONTROLLER.MD) | Class        | Instruct a class to be a controller    |
| [Accepts](Attributes/ACCEPTS.MD)                  | Class/Method | Specify the accepted content type      |
| [Path](Attributes/PATH.MD)                        | Class/Method | Specify the URL path                   |
| [Middleware](Attributes/MIDDLEWARE.MD)           | Class/Method | Bind a middleware operation            |
| [Method](Attributes/METHOD.MD)                    | Method       | Specify the HTTP method for that route |
