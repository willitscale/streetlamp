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

        $columnHeaders = [
            'Method',
            'Path',
            'Accepts',
            'Class',
            'Function'
        ];

        $columnBreaksLength = (count($columnHeaders) - 1) * 3 + 4;
        $columnLengths = [];

        for($i = 0; $i < count($columnHeaders); $i++) {
            $columnLengths[$i] = strlen($columnHeaders[$i]);
        }

        foreach ($routeBuilder->getRoutes() as $route) {
            $columnLengths[0] = max(strlen($route->getMethod()), $columnLengths[0]);
            $columnLengths[1] = max(strlen($route->getPath()), $columnLengths[1]);
            $columnLengths[2] = max(strlen($route->getAccepts() ?? ''), $columnLengths[2]);
            $columnLengths[3] = max(strlen($route->getClass()), $columnLengths[3]);
            $columnLengths[4] = max(strlen($route->getFunction()), $columnLengths[4]);
        }

        $totalLength = array_sum($columnLengths) + $columnBreaksLength;

        $this->printTableBreak($totalLength);
        $this->printRow($columnHeaders, $columnLengths);
        $this->printTableBreak($totalLength);

        foreach ($routeBuilder->getRoutes() as $route) {
            $row = [
                $route->getMethod(),
                $route->getPath(),
                $route->getAccepts() ?? '',
                $route->getClass(),
                $route->getFunction()
            ];
            $this->printRow($row, $columnLengths);
        }

        $this->printTableBreak($totalLength);
    }

    private function printRow(array $row, array $columnLengths): void
    {
        for($i = 0; $i < count($row); $i++) {
            $row[$i] = str_pad($row[$i], $columnLengths[$i]);
        }

        echo '| ', implode(' | ', $row), ' |', PHP_EOL;
    }

    private function printTableBreak($length): void
    {
        echo str_repeat('-', $length), PHP_EOL;
    }
}
