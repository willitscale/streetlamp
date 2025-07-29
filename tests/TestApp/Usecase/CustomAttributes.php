<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\TestApp\Usecase;

use willitscale\StreetlampTests\TestApp\Attributes\CustomAttribute;

class CustomAttributes
{
    #[CustomAttribute('attribute1', 'This is attribute 1')]
    public function attribute1(): void
    {
    }

    #[CustomAttribute('attribute2', 'This is attribute 2')]
    public function attribute2(): void
    {
    }
}
