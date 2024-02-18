<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands;

use willitscale\Streetlamp\Commands\Routes\Cache;
use willitscale\Streetlamp\Commands\Routes\Lists;

class Routes extends Command implements CommandInterface
{
    private const ERROR_MESSAGE = "Missing action for routes command:";

    private const AVAILABLE_COMMANDS = [
        'lists' => 'Lists all available routes.',
        'cache' => 'Route cache operations.'
    ];

    public function __construct(
        protected readonly Lists $lists,
        protected readonly Cache $cache
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
