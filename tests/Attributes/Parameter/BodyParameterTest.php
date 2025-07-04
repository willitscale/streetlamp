<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Parameter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Attributes\Parameter\BodyParameter;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequireBodyException;
use PHPUnit\Framework\TestCase;

class BodyParameterTest extends ParameterTestCase
{
    #[Test]
    #[DataProvider('validValues')]
    public function testAValueIsExtractedCorrectlyFromTheBody(
        mixed $expectedValue,
        string $dataType
    ): void {
        $request = $this->createServerRequest($this->createStreamWithContents((string)$expectedValue));
        $bodyArgument = new BodyParameter(false, []);
        $bodyArgument->setType($dataType);
        $returnedValue = $bodyArgument->getValue([], $request);
        $this->assertEquals($expectedValue, $returnedValue);
    }

    #[Test]
    public function testThatAnExceptionIsThrownWhenThereIsNoOrAnEmptyBody(): void
    {
        $request = $this->createServerRequest($this->createStreamWithContents(''));
        $this->expectException(MissingRequireBodyException::class);
        $bodyArgument = new BodyParameter();
        $bodyArgument->getValue([], $request);
    }

    public static function validValues(): array
    {
        return [
            'it should use the file system instead of the input stream and extract the correct string value' => [
                'expectedValue' => 'test',
                'dataType' => 'string',
            ],
            'it should use the file system instead of the input stream and extract the correct int value' => [
                'expectedValue' => 44,
                'dataType' => 'int',
            ],
            'it should use the file system instead of the input stream and extract the correct float value' => [
                'expectedValue' => 1.1,
                'dataType' => 'float',
            ],
            'it should use the file system instead of the input stream and extract the correct bool value' => [
                'expectedValue' => true,
                'dataType' => 'bool',
            ]
        ];
    }
}
