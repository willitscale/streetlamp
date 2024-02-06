<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands\Init\Servers;

use willitscale\Streetlamp\Commands\CommandInterface;

class Apache implements CommandInterface
{

    public function command(?array $arguments = []): void
    {
        mkdir($_SERVER['PWD'] . '/docker/apache', 0777, true);
        copy(__DIR__ . '/../templates/apache.conf.tmpl', $_SERVER['PWD'] . '/docker/apache/default.conf');
        $dockerCompose = file_get_contents(__DIR__ . '/../templates/docker-compose.yml.tmpl');
        preg_replace("/({APACHE})([^{]+)({\/APACHE})/", "$2", $dockerCompose);
        preg_replace("/{[A-Z]+}[^{]+\{\/[A-Z]+}/", "", $dockerCompose);
        file_put_contents($_SERVER['PWD'] . '/docker-compose.yml', $dockerCompose);
    }
}
