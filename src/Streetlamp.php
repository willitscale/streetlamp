<?php

declare(strict_types=1);

namespace willitscale\Streetlamp;

use willitscale\Streetlamp\Builders\RouterConfigBuilder;

final readonly class Streetlamp
{
    private const ALLOWED_COMMANDS = [
        'init' => 'Initialise a resource.',
        'routes' => 'List all available routes for the application.'
    ];

    private const INIT_ACTIONS = [
        'docker' => 'Scaffold a basic docker application.'
    ];

    private const ROUTE_ACTIONS = [
        'list' => 'List all available routes'
    ];

    public function __construct(int $argumentCount, array $arguments)
    {
        if (3 > $argumentCount) {
            $this->missingArgument(
                "Expected at least 1 argument in the form of COMMAND ...",
                self::ALLOWED_COMMANDS
            );
        }

        array_shift($arguments);
        $command = array_shift($arguments);
        $action = array_shift($arguments);

        $this->command($command, $action, $arguments);
    }

    private function command(string $command, string $action, ?array $arguments = []): void
    {
        switch ($command) {
            case 'init':
                $this->init($action, $arguments);
                break;
            case 'routes':
                $this->routes($action, $arguments);
                break;
        }
    }

    private function init(string $action, ?array $arguments = []): void
    {
        switch ($action) {
            case 'docker':
                $this->docker($arguments);
                break;
            default:
                $this->missingArgument(
                    "Invalid action for init command",
                    self::INIT_ACTIONS
                );
        }
    }

    private function docker(?array $arguments = []): void
    {
        echo "Setting up Docker for Streetlamp", PHP_EOL;

        mkdir($_SERVER['PWD'] . '/docker/nginx', 0777, true);
        copy(__DIR__ . '/../templates/nginx.conf.tmpl', $_SERVER['PWD'] . '/docker/nginx/default.conf');
        copy(__DIR__ . '/../templates/docker-compose.yml.tmpl', $_SERVER['PWD'] . '/docker-compose.yml');

        echo "Done. Run `docker compose up` to start your application locally and go to http://localhost once it's finished.", PHP_EOL;
    }

    private function routes(string $action, ?array $arguments = []):void
    {
        switch ($action) {
            case 'list':
                $this->listRoutes($arguments);
                break;
            default:
                $this->missingArgument(
                    "Invalid action for routes command",
                    self::ROUTE_ACTIONS
                );
        }
    }

    private function listRoutes(?array $arguments = []): void
    {
        $routerConfig = new RouterConfigBuilder();

        if (0 < count($arguments)) {
            $routerConfig->setRootDirectory(array_shift($arguments));
        }

        if (0 < count($arguments)) {
            $routerConfig->setComposerFile(array_shift($arguments));
        }

        $routeBuilder = new RouteBuilder($routerConfig->build());

        $methodColumn = 'Method';
        $pathColumn = 'Path';
        $acceptsColumn = 'Accepts';
        $classColumn = 'Class';
        $functionColumn = 'Function';

        $columnBreaksLength = 16;

        $maxMethodLength = strlen($methodColumn);
        $maxPathLength = strlen($pathColumn);
        $maxAcceptsLength = strlen($acceptsColumn);
        $maxClassLength = strlen($classColumn);
        $maxFunctionLength = strlen($functionColumn);

        foreach($routeBuilder->getRoutes() as $route) {
            $maxMethodLength = max(strlen($route->getMethod()), $maxMethodLength);
            $maxPathLength = max(strlen($route->getPath()), $maxPathLength);
            $maxAcceptsLength = max(strlen($route->getAccepts() ?? ''), $maxAcceptsLength);
            $maxClassLength = max(strlen($route->getClass()), $maxClassLength);
            $maxFunctionLength = max(strlen($route->getFunction()), $maxFunctionLength);
        }

        $totalLength = $maxMethodLength +
            $maxPathLength +
            $maxAcceptsLength +
            $maxClassLength +
            $maxFunctionLength +
            $columnBreaksLength;

        $this->printTableBreak($totalLength);

        echo '| ',
            str_pad($methodColumn, $maxMethodLength), ' | ',
            str_pad($pathColumn, $maxPathLength), ' | ',
            str_pad($acceptsColumn, $maxAcceptsLength), ' | ',
            str_pad($classColumn, $maxClassLength), ' | ',
            str_pad($functionColumn, $maxFunctionLength), ' |', PHP_EOL;

        $this->printTableBreak($totalLength);

        foreach($routeBuilder->getRoutes() as $route) {
            echo '| ',
                str_pad($route->getMethod(), $maxMethodLength), ' | ',
                str_pad($route->getPath(), $maxPathLength), ' | ',
                str_pad($route->getAccepts() ?? '', $maxAcceptsLength),  ' | ',
                str_pad($route->getClass(), $maxClassLength), ' | ',
                str_pad($route->getFunction(), $maxFunctionLength), ' |', PHP_EOL;
        }

        $this->printTableBreak($totalLength);
    }

    private function printTableBreak($length): void
    {
        echo str_repeat('-', $length), PHP_EOL;
    }

    private function missingArgument(string $message, array $options): void
    {
        echo $message, PHP_EOL,
        "All available arguments: ", PHP_EOL;
        foreach ($options as $argument => $description) {
            echo '  ', str_pad($argument, 10), ' - ', $description, PHP_EOL;
        }
        exit(1);
    }
}
