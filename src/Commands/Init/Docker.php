<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands\Init;

use willitscale\Streetlamp\Commands\CommandInterface;

class Docker implements CommandInterface
{
    public function command(?array $arguments = []): void
    {
        echo "Setting up Docker for Streetlamp", PHP_EOL;

        mkdir($_SERVER['PWD'] . '/docker/nginx', 0777, true);
        copy(__DIR__ . '/../templates/nginx.conf.tmpl', $_SERVER['PWD'] . '/docker/nginx/default.conf');
        copy(__DIR__ . '/../templates/docker-compose.yml.tmpl', $_SERVER['PWD'] . '/docker-compose.yml');

        echo "Done. Run `docker compose up` to start your application locally and go to http://localhost once it's finished.", PHP_EOL;
    }
}
