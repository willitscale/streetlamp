<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Parameter;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use willitscale\Streetlamp\Requests\ServerRequest;
use willitscale\Streetlamp\Requests\Stream;
use willitscale\Streetlamp\Requests\Uri;

class ParameterTestCase extends TestCase
{
    protected function createServerRequest(
        ?StreamInterface $stream = null,
        array $headers = [],
        array $queryParams = [],
        array $cookies = [],
        array $serverParams = [],
        array $uploadedFiles = [],
        array $postParams = [],
        string $protocolVersion = '1.1'
    ): ServerRequest {
        return new ServerRequest(
            'GET',
            new Uri('http://example.com'),
            $stream,
            $headers,
            $protocolVersion,
            $serverParams,
            $cookies,
            $queryParams,
            $uploadedFiles,
            $postParams
        );
    }

    protected function createStreamWithContents(string $contents): StreamInterface
    {
        $stream = new Stream('php://temp', 'rw+');
        $stream->write($contents);
        $stream->rewind();
        return $stream;
    }
}
