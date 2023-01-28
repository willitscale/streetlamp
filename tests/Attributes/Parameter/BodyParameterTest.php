<?php declare(strict_types=1);

namespace Attributes\Parameter;

use n3tw0rk\Streetlamp\Attributes\Parameter\BodyParameter;
use n3tw0rk\Streetlamp\Exceptions\InvalidParameterTypeException;
use n3tw0rk\Streetlamp\Exceptions\Parameters\MissingRequireBodyException;
use n3tw0rk\Streetlamp\Exceptions\Validators\InvalidParameterFailedToPassFilterValidation;
use PHPUnit\Framework\TestCase;

class BodyParameterTest extends TestCase
{
    /**
     * @param bool|int|float|string $expectedValue
     * @param string $dataType
     * @param string $resourceIdentifier
     * @return void
     * @throws InvalidParameterTypeException
     * @throws InvalidParameterFailedToPassFilterValidation
     * @dataProvider validValues
     */
    public function testAValueIsExtractedCorrectlyFromTheBody(
        bool|int|float|string $expectedValue,
        string $dataType,
        string $resourceIdentifier
    ): void {
        file_put_contents($resourceIdentifier, $expectedValue);
        $bodyArgument = new BodyParameter([], $resourceIdentifier);
        $bodyArgument->setType($dataType);
        $returnedValue = $bodyArgument->getValue([]);
        $this->assertEquals($expectedValue, $returnedValue);
        if (file_exists($resourceIdentifier)) {
            unlink($resourceIdentifier);
        }
    }

    /**
     * @return void
     * @throws InvalidParameterFailedToPassFilterValidation
     * @throws InvalidParameterTypeException
     */
    public function testThatAnExceptionIsThrownWhenThereIsNoOrAnEmptyBody(): void
    {
        $this->expectException(MissingRequireBodyException::class);
        $bodyArgument = new BodyParameter();
        $bodyArgument->getValue([]);
    }

    public function validValues(): array
    {
        return [
            'it should use the file system instead of the input stream and extract the correct string value' => [
                'expectedValue' => 'test',
                'dataType' => 'string',
                'resourceIdentifier' => __DIR__ . '/unittest.dat',
            ],
            'it should use the file system instead of the input stream and extract the correct int value' => [
                'expectedValue' => 44,
                'dataType' => 'int',
                'resourceIdentifier' => __DIR__ . '/unittest.dat',
            ],
            'it should use the file system instead of the input stream and extract the correct float value' => [
                'expectedValue' => 1.1,
                'dataType' => 'float',
                'resourceIdentifier' => __DIR__ . '/unittest.dat',
            ],
            'it should use the file system instead of the input stream and extract the correct bool value' => [
                'expectedValue' => true,
                'dataType' => 'bool',
                'resourceIdentifier' => __DIR__ . '/unittest.dat',
            ]
        ];
    }
}
