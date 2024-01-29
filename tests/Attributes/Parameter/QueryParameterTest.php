<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Parameter;

use willitscale\Streetlamp\Attributes\Parameter\QueryParameter;
use willitscale\Streetlamp\Attributes\Validators\FilterVarsValidator;
use willitscale\Streetlamp\Attributes\Validators\ValidatorInterface;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequireQueryException;
use PHPUnit\Framework\TestCase;

class QueryParameterTest extends TestCase
{
    /**
     * @param string $key
     * @param string $inputValue
     * @param bool|int|float|string $expectedValue
     * @param string $dataType
     * @param ValidatorInterface[] $validators
     * @return void
     * @dataProvider validValues
     */
    public function testAValueIsExtractedCorrectlyFromPost(
        string $key,
        string $inputValue,
        bool|int|float|string $expectedValue,
        string $dataType,
        array $validators
    ): void {
        $_GET[$key] = $inputValue;
        $queryArgument = new QueryParameter($key, $validators);
        $queryArgument->setType($dataType);
        $returnedValue = $queryArgument->getValue([
            $key => $inputValue
        ]);
        $this->assertEquals($expectedValue, $returnedValue);
        unset($_GET[$key]);
    }

    /**
     * @return void
     */
    public function testThatAnExceptionIsThrownWhenAMissingPostIsSpecified(): void
    {
        $this->expectException(MissingRequireQueryException::class);
        $queryArgument = new QueryParameter('test');
        $queryArgument->getValue([]);
    }

    public static function validValues(): array
    {
        return [
            'it should set a string value and extract a matching value' => [
                'key' => 'test',
                'inputValue' => 'test',
                'expectedValue' => 'test',
                'dataType' => 'string',
                'validators' => []
            ],
            'it should set an int value and extract a matching value' => [
                'key' => 'test',
                'inputValue' => '321',
                'expectedValue' => 321,
                'dataType' => 'int',
                'validators' => []
            ],
            'it should set a float value and extract a matching value' => [
                'key' => 'test',
                'inputValue' => '1.23',
                'expectedValue' => 1.23,
                'dataType' => 'float',
                'validators' => []
            ],
            'it should set a bool value and extract a matching value' => [
                'key' => 'test',
                'inputValue' => '1',
                'expectedValue' => true,
                'dataType' => 'bool',
                'validators' => []
            ],
            'it should set the a string value and extract a numeric value' => [
                'key' => 'test',
                'inputValue' => '123test',
                'expectedValue' => 123,
                'dataType' => 'int',
                'validators' => [new FilterVarsValidator(FILTER_SANITIZE_NUMBER_INT)]
            ]
        ];
    }
}
