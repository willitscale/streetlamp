<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Controllers;

use Psr\Http\Message\StreamInterface;
use willitscale\Streetlamp\Requests\Stream;
use willitscale\StreetlampTest\RouteTestCase;

class ControllerTestCase extends RouteTestCase
{
    protected const array COMPOSER_TEST_FILE = [__DIR__, '..', 'TestApp', 'composer.test.json'];
    protected const array TEST_ROOT = [__DIR__, '..'];


    protected function getTestRoot(): string
    {
        return implode(DIRECTORY_SEPARATOR, self::TEST_ROOT);
    }

    protected function getComposerTestFile(): string
    {
        return implode(DIRECTORY_SEPARATOR, self::COMPOSER_TEST_FILE);
    }

    protected function createStreamWithContents(string $contents): StreamInterface
    {
        $stream = new Stream('php://temp', 'wr+');
        $stream->write($contents);
        $stream->rewind();
        return $stream;
    }
}
