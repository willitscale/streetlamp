<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands;

use willitscale\Streetlamp\Commands\Init\Docker;

class Init extends Command implements CommandInterface
{
    private const ERROR_MESSAGE = "Missing action for init command:";
    private const AVAILABLE_COMMANDS = [
        'docker' => 'Scaffold a basic docker application.'
    ];

    public function __construct(
        protected readonly Docker $docker
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
    }
}
