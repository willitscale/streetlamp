<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Parameter;

use PHPUnit\Framework\Attributes\DataProvider;
use willitscale\Streetlamp\Attributes\Parameter\PostParameter;
use willitscale\Streetlamp\Attributes\Validators\FilterVarsValidator;
use willitscale\Streetlamp\Exceptions\Parameters\MissingRequiredPostException;
use PHPUnit\Framework\TestCase;

class PostParameterTest extends TestCase
{
    #[DataProvider('validValues')]
    public function testAValueIsExtractedCorrectlyFromPost(
        string $key,
        string $inputValue,
        bool|int|float|string $expectedValue,
        string $dataType,
        array $validators
    ): void {
        $_POST[$key] = $inputValue;
        $postArgument = new PostParameter($key, $validators);
        $postArgument->setType($dataType);
        $returnedValue = $postArgument->getValue([
            $key => $inputValue
        ]);
        $this->assertEquals($expectedValue, $returnedValue);
        unset($_POST[$key]);
    }

    public function testThatAnExceptionIsThrownWhenAMissingPostIsSpecified(): void
    {
        $this->expectException(MissingRequiredPostException::class);
        $postArgument = new PostParameter('string', []);
        $postArgument->getValue([]);
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
