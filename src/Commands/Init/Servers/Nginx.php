<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands\Init\Servers;

use willitscale\Streetlamp\Commands\CommandInterface;

class Nginx implements CommandInterface
{
    const ROOT_DIR = __DIR__ . '/../../../../';

    public function command(?array $arguments = []): void
    {
        $nginxDir = $_SERVER['PWD'] . '/docker/nginx';

        if (!file_exists($nginxDir)) {
            mkdir($nginxDir, 0777, true);
        }

        copy(self::ROOT_DIR . 'templates/nginx/nginx.conf.tmpl', $_SERVER['PWD'] . '/docker/nginx/default.conf');
        copy(self::ROOT_DIR . 'templates/nginx/Dockerfile.tmpl', $_SERVER['PWD'] . '/docker/nginx/Dockerfile');

        $dockerCompose = file_get_contents(self::ROOT_DIR . 'templates/docker-compose.yml.tmpl');

        $dockerCompose = preg_replace("/({NGINX})([^{]+)({\/NGINX})/", "$2", $dockerCompose);
        $dockerCompose = preg_replace("/{[A-Z]+}[^{]+\{\/[A-Z]+}\n/m", "", $dockerCompose);

        file_put_contents($_SERVER['PWD'] . '/docker-compose.yml', $dockerCompose);
    }
}
