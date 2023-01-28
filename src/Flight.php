<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp;

use n3tw0rk\Streetlamp\Builders\ResponseBuilder;
use n3tw0rk\Streetlamp\Requests\RequestInterface;

interface Flight
{
    public function pre(RequestInterface $request);
    public function post(ResponseBuilder $response);
}
