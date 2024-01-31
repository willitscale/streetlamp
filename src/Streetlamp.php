<?php

declare(strict_types=1);

namespace willitscale\Streetlamp;

use willitscale\Streetlamp\Commands\Command;
use willitscale\Streetlamp\Commands\CommandInterface;
use willitscale\Streetlamp\Commands\Init;
use willitscale\Streetlamp\Commands\Routes;

class Streetlamp extends Command implements CommandInterface
{
    private const ERROR_MESSAGE = "Expected at least 1 argument in the form of COMMAND ...";

    private const AVAILABLE_COMMANDS = [
        'init' => 'Initialise a resource.',
        'routes' => 'List all available routes for the application.'
    ];

    public function __construct(
        readonly protected Init $init,
        readonly protected Routes $routes
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
            self::ERROR_MESSAGE,
        );
    }
}
