<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Parameter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use willitscale\Streetlamp\Attributes\Parameter\HeaderParameter;
use willitscale\Streetlamp\Attributes\Validators\FilterVarsValidator;
use willitscale\Streetlamp\Attributes\Validators\ValidatorInterface;
use willitscale\Streetlamp\Exceptions\InvalidParameterTypeException;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequiredHeaderException;
use willitscale\Streetlamp\Exceptions\Validators\InvalidParameterFailedToPassFilterValidation;
use PHPUnit\Framework\TestCase;

class HeaderParameterTest extends TestCase
{
    #[Test]
    #[DataProvider('validValues')]
    public function testAValueIsExtractedCorrectlyFromHeaders(
        string $key,
        bool $required,
        string $inputValue,
        bool|int|float|string $expectedValue,
        string $dataType,
        array $validators
    ): void {
        $serverKey = 'HTTP_' . strtoupper($key);
        $_SERVER[$serverKey] = $inputValue;
        $headerArgument = new HeaderParameter($key, $required, $validators);
        $headerArgument->setType($dataType);
        $returnedValue = $headerArgument->getValue([]);
        $this->assertEquals($expectedValue, $returnedValue);
        unset($_SERVER[$serverKey]);
    }

    #[Test]
    public function testThatAnExceptionIsThrownWhenAMissingHeaderIsSpecified(): void
    {
        $this->expectException(MissingRequiredHeaderException::class);
        $headerArgument = new HeaderParameter('string', true, []);
        $headerArgument->getValue([]);
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
