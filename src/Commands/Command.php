<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands;

use Exception;
use willitscale\Streetlamp\Builders\RouteBuilder;
use willitscale\Streetlamp\Builders\RouterConfigBuilder;
use willitscale\Streetlamp\Requests\ServerRequest;
use willitscale\Streetlamp\Requests\Uri;

abstract class Command
{
    protected function popArgument(?array &$arguments, array $availableCommands, string $errorMessage): string
    {
        if (empty($arguments)) {
            $this->missingArgument(
                $errorMessage,
                $availableCommands
            );
        }

        return array_shift($arguments);
    }

    protected function missingArgument(string $message, array $options): void
    {
        $message .= PHP_EOL . "All available arguments: " . PHP_EOL;

        foreach ($options as $argument => $description) {
            $message .= '  ' . str_pad($argument, 10) . ' - ' . $description . PHP_EOL;
        }

        throw new Exception($message);
    }

    protected function execute(
        string $command,
        ?array $arguments,
        array $availableCommands,
        string $errorMessage
    ): void {
        if (!array_key_exists($command, $availableCommands)) {
            $this->missingArgument(
                $errorMessage,
                $availableCommands
            );
        }

        $this->{$command}->command($arguments);
    }

    protected function buildRouteBuilderFromArguments(?array $arguments = []): RouteBuilder
    {
        $request = new ServerRequest(
            'GET',
            new Uri('/'),
            null,
            [],
            '1.1',
            []
        );

        $routerConfig = new RouterConfigBuilder()
            ->setRequest($request);

        if (0 < count($arguments)) {
            $routerConfig->setRootDirectory(array_shift($arguments));
        }

        if (0 < count($arguments)) {
            $routerConfig->setComposerFile(array_shift($arguments));
        }

        return new RouteBuilder($routerConfig->build());
    }
}
