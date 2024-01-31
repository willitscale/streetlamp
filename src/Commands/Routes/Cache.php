<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands\Routes;

use willitscale\Streetlamp\Commands\Command;
use willitscale\Streetlamp\Commands\CommandInterface;
use willitscale\Streetlamp\Commands\Routes\Cache\Clear;

class Cache extends Command implements CommandInterface
{
    private const ERROR_MESSAGE = "Missing operation for cache action:";
    private const AVAILABLE_COMMANDS = [
        'clear' => 'Clear the route cache.'
    ];

    public function __construct(
        readonly protected Clear $clear
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
