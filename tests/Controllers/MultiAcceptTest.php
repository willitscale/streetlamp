<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Controllers;

use PHPUnit\Framework\Attributes\Test;

class MultiAcceptTest extends ControllerTestCase
{
    #[Test]
    public function itShouldReturnPongForPingWithMultiAcceptHeaderRequest(): void
    {
        $router = $this->setupRouter(
            'GET',
            '/ping',
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            ['Content-Type' => 'application/json, text/event-stream']
        );

        $response = $router->route();
        $this->assertEquals('pong', (string) $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
