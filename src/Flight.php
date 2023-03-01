<?php

declare(strict_types=1);

namespace willitscale\Streetlamp;

use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Requests\RequestInterface;

interface Flight
{
    public function pre(RequestInterface $request);
    public function post(ResponseBuilder $response);
}
