<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use willitscale\Streetlamp\Attributes\Validators\FloatValidator;

class FloatValidatorTest extends TestCase
{
    #[DataProvider('validateScenarios')]
    public function testThatValidateCorrectlyValidatesTheInput(
        string $input,
        bool $expectedResult,
        ?float $min = 0,
        ?float $max = PHP_FLOAT_MAX
    ): void {
        $regExpValidator = new FloatValidator($min, $max);
        $response = $regExpValidator->validate($input);
        $this->assertEquals($expectedResult, $response);
    }

    #[DataProvider('sanitizeScenarios')]
    public function testThatSanitizeCorrectlySanitizesTheInput(
        string $input,
        float $expectedResult
    ): void {
        $regExpValidator = new FloatValidator();
        $response = $regExpValidator->sanitize($input);
        $this->assertEquals($expectedResult, $response);
    }

    public static function validateScenarios(): array
    {
        return [
            "it should validate that the value passed is a valid float" => [
                "input" => "123.0",
                "expectedResult" => true
            ],
            "it should fail to validate the value passed is a valid float" => [
                "input" => "123a",
                "expectedResult" => false
            ],
            "it should validate that the value passed is a valid float between the min and max thresholds" => [
                "input" => "123.45",
                "expectedResult" => true,
                "min" => 123.0,
                "max" => 124.0
            ],
            "it should fail to validate a value passed which is a valid float but not between the min and max thresholds" => [
                "input" => "123.0",
                "expectedResult" => false,
                "min" => 123.1,
                "max" => 124.0
            ],
        ];
    }

    public static function sanitizeScenarios(): array
    {
        return [
            "it should validate that a string sanitizes to the float equivalent" => [
                "input" => "123.0",
                "expectedResult" => 123
            ]
        ];
    }
}
