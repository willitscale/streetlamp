<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Parameter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Attributes\Parameter\QueryParameter;
use willitscale\Streetlamp\Attributes\Validators\FilterVarsValidator;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequireQueryException;

class QueryParameterTest extends ParameterTestCase
{
    #[Test]
    #[DataProvider('validValues')]
    public function testAValueIsExtractedCorrectlyFromPost(
        string $key,
        bool $required,
        string $inputValue,
        bool|int|float|string $expectedValue,
        string $dataType,
        array $validators
    ): void {
        $request = $this->createServerRequest(
            null,
            [],
            [
                $key => $inputValue
            ]
        );
        $queryArgument = new QueryParameter($key, $required, $validators);
        $queryArgument->setType($dataType);
        $returnedValue = $queryArgument->getValue([
            $key => $inputValue
        ], $request);
        $this->assertEquals($expectedValue, $returnedValue);
    }

    #[Test]
    public function testThatAnExceptionIsThrownWhenAMissingPostIsSpecified(): void
    {
        $request = $this->createServerRequest();
        $this->expectException(MissingRequireQueryException::class);
        $queryArgument = new QueryParameter('test');
        $queryArgument->getValue([], $request);
    }

    public static function validValues(): array
    {
        return [
            'it should set a string value and extract a matching value' => [
                'key' => 'test',
                'required' => true,
                'inputValue' => 'test',
                'expectedValue' => 'test',
                'dataType' => 'string',
                'validators' => []
            ],
            'it should set an int value and extract a matching value' => [
                'key' => 'test',
                'required' => true,
                'inputValue' => '321',
                'expectedValue' => 321,
                'dataType' => 'int',
                'validators' => []
            ],
            'it should set a float value and extract a matching value' => [
                'key' => 'test',
                'required' => true,
                'inputValue' => '1.23',
                'expectedValue' => 1.23,
                'dataType' => 'float',
                'validators' => []
            ],
            'it should set a bool value and extract a matching value' => [
                'key' => 'test',
                'required' => true,
                'inputValue' => '1',
                'expectedValue' => true,
                'dataType' => 'bool',
                'validators' => []
            ],
            'it should set the a string value and extract a numeric value' => [
                'key' => 'test',
                'required' => true,
                'inputValue' => '123test',
                'expectedValue' => 123,
                'dataType' => 'int',
                'validators' => [new FilterVarsValidator(FILTER_SANITIZE_NUMBER_INT)]
            ]
        ];
    }
}
