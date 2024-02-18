<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands\Init\Servers;

use willitscale\Streetlamp\Commands\CommandInterface;

class Apache implements CommandInterface
{
    const ROOT_DIR = __DIR__ . '/../../../../';

    public function command(?array $arguments = []): void
    {
        $nginxDir = $_SERVER['PWD'] . '/docker/apache';

        if (!file_exists($nginxDir)) {
            mkdir($nginxDir, 0777, true);
        }

        copy(self::ROOT_DIR . 'templates/apache/apache.conf.tmpl', $_SERVER['PWD'] . '/docker/apache/default.conf');
        copy(self::ROOT_DIR . 'templates/apache/Dockerfile.tmpl', $_SERVER['PWD'] . '/docker/apache/Dockerfile');

        $dockerCompose = file_get_contents(self::ROOT_DIR . 'templates/docker-compose.yml.tmpl');

        $dockerCompose = preg_replace("/({APACHE})([^{]+)({\/APACHE})/", "$2", $dockerCompose);
        $dockerCompose = preg_replace("/{[A-Z]+}[^{]+\{\/[A-Z]+}\n/m", "", $dockerCompose);

        file_put_contents($_SERVER['PWD'] . '/docker-compose.yml', $dockerCompose);
    }
}
