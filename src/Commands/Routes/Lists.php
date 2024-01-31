<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands\Routes;

use willitscale\Streetlamp\Commands\Command;
use willitscale\Streetlamp\Commands\CommandInterface;

class Lists extends Command implements CommandInterface
{

    public function command(?array $arguments = []): void
    {
        $routeBuilder = $this->buildRouteBuilderFromArguments($arguments);

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

        foreach ($routeBuilder->getRoutes() as $route) {
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

        foreach ($routeBuilder->getRoutes() as $route) {
            echo '| ',
            str_pad($route->getMethod(), $maxMethodLength), ' | ',
            str_pad($route->getPath(), $maxPathLength), ' | ',
            str_pad($route->getAccepts() ?? '', $maxAcceptsLength), ' | ',
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
