<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands\Init;

use willitscale\Streetlamp\Commands\Command;
use willitscale\Streetlamp\Commands\CommandInterface;
use willitscale\Streetlamp\Commands\Init\Servers\Apache;
use willitscale\Streetlamp\Commands\Init\Servers\Nginx;

class Docker extends Command implements CommandInterface
{
    private const string ERROR_MESSAGE = "Missing operation for docker action:";
    private const array AVAILABLE_COMMANDS = [
        'nginx' => 'Initialise a docker environment with Nginx.',
        'apache' => 'Initialise a docker environment with Apache.'
    ];

    public function __construct(
        readonly protected Apache $apache,
        readonly protected Nginx $nginx
    ) {
    }

    public function command(?array $arguments = []): void
    {
        $command = $this->popArgument(
            $arguments,
            self::AVAILABLE_COMMANDS,
            self::ERROR_MESSAGE
        );

        $this->execute(
            $command,
            $arguments,
            self::AVAILABLE_COMMANDS,
            self::ERROR_MESSAGE
        );

        echo "Done. Run `docker compose up` to start your application locally and go to http://localhost once it's finished.", PHP_EOL;
    }
}
