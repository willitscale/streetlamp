<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Parameter;

use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Attributes\Parameter\FileParameter;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequiredFilesException;
use willitscale\Streetlamp\Models\File;

class FileParameterTest extends ParameterTestCase
{
    #[Test]
    public function testAValueIsExtractedCorrectlyFromFiles(): void
    {
        $key = 'test_file.png';
        $data = [
            'name' => $key,
            'path' => '/tmp/' . hash('sha1', $key),
            'type' => 'text/plain',
            'tmp_name' => '/tmp/' . hash('sha1', $key),
            'error' => 0,
            'size' => rand(1000, 10000),
        ];

        $request = $this->createServerRequest(
            null,
            [],
            [],
            [],
            [],
            [
                $key => $data
            ]
        );

        $fileArgument = new FileParameter($key);
        $fileArgument->setType(File::class);
        $returnedValue = $fileArgument->getValue([], $request);
        $this->assertEquals(json_encode($data), json_encode($returnedValue));
    }

    #[Test]
    public function testThatAnExceptionIsThrownWhenAMissingFileIsSpecified(): void
    {
        $request = $this->createServerRequest();
        $this->expectException(MissingRequiredFilesException::class);
        $fileArgument = new FileParameter('param');
        $fileArgument->getValue([], $request);
    }
}
