<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\ResponseTypes;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use willitscale\Streetlamp\Models\Route;

interface ResponseTypeInterface
{
    public function execute(
        Route $route,
        ServerRequestInterface $request,
        array $args,
    ): ResponseInterface;
}
