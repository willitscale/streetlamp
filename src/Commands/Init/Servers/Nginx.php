<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands\Init\Servers;

use willitscale\Streetlamp\Commands\CommandInterface;

class Nginx implements CommandInterface
{
    public function command(?array $arguments = []): void
    {
        mkdir($_SERVER['PWD'] . '/docker/nginx', 0777, true);
        copy(__DIR__ . '/../templates/nginx.conf.tmpl', $_SERVER['PWD'] . '/docker/nginx/default.conf');
        $dockerCompose = file_get_contents(__DIR__ . '/../templates/docker-compose.yml.tmpl');
        preg_replace("/({NGINX})([^{]+)({\/NGINX})/", "$2", $dockerCompose);
        preg_replace("/{[A-Z]+}[^{]+\{\/[A-Z]+}/", "", $dockerCompose);
        file_put_contents($_SERVER['PWD'] . '/docker-compose.yml', $dockerCompose);
    }
}
