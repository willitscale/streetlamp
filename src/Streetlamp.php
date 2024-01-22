<?php

declare(strict_types=1);

namespace willitscale\Streetlamp;

use willitscale\Streetlamp\Builders\RouterConfigBuilder;
use willitscale\Streetlamp\Models\RouterConfig;

final readonly class Streetlamp
{
    private const ALLOWED_COMMANDS = [
        'init' => 'Initialise a resource.',
        'routes' => 'List all available routes for the application.'
    ];

    public function __construct(int $argumentCount, array $arguments)
    {
        if (3 > $argumentCount) {
            echo "Expected at least 2 arguments in the form of COMMAND ACTION ...", PHP_EOL,
                 "All available commands: ", PHP_EOL;

            foreach (self::ALLOWED_COMMANDS as $command => $description) {
                echo '  ', str_pad($command, 10), ' - ', $description, PHP_EOL;
            }
            exit(1);
        }

        array_shift($arguments);
        $command = array_shift($arguments);
        $action = array_shift($arguments);

        $this->command($command, $action, $arguments);
    }

    public function command(string $command, string $action, ?array $arguments = []): void
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
                echo "Invalid action", PHP_EOL;
                exit(1);
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
                echo "Invalid action", PHP_EOL;
                exit(1);
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
        $maxClassLength = strlen($acceptsColumn);
        $maxFunctionLength = strlen($classColumn);
        $maxAcceptsLength = strlen($functionColumn);

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
}
