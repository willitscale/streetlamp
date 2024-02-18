<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Commands\Routes\Cache;

use willitscale\Streetlamp\Commands\Command;
use willitscale\Streetlamp\Commands\CommandInterface;

class Clear extends Command implements CommandInterface
{
    public function command(?array $arguments = []): void
    {
        $routeBuilder = $this->buildRouteBuilderFromArguments($arguments);
        $routeBuilder->clearCachedConfig();
    }
}
