<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use willitscale\Streetlamp\Attributes\Validators\FilterVarsValidator;

class FilterValidatorTest extends TestCase
{
    #[DataProvider('validateScenarios')]
    public function testThatValidateCorrectlyValidatesTheInput(
        int $filter,
        string $input,
        bool $expectedResult,
        int|array $options = 0
    ): void {
        $regExpValidator = new FilterVarsValidator($filter, $options);
        $response = $regExpValidator->validate($input);
        $this->assertEquals($expectedResult, $response);
    }

    #[DataProvider('sanitizeScenarios')]
    public function testThatSanitizeCorrectlySanitizesTheInput(
        int $filter,
        string $input,
        string $expectedResult,
        int|array $options = 0
    ): void {
        $regExpValidator = new FilterVarsValidator($filter, $options);
        $response = $regExpValidator->sanitize($input);
        $this->assertEquals($expectedResult, $response);
    }

    public static function validateScenarios(): array
    {
        return [
            "it should validate that a string containing a URL filters correctly" => [
                "filter" => FILTER_VALIDATE_URL,
                "input" => "https://www.example.com",
                "expectedResult" => true
            ],
            "it should fail to validate a string that contains an invalid URL" => [
                "filter" => FILTER_VALIDATE_URL,
                "input" => "invalid::/url",
                "expectedResult" => false
            ]
        ];
    }

    public static function sanitizeScenarios(): array
    {
        return [
            "it should validate that a date string matches correctly" => [
                "filter" => FILTER_SANITIZE_ENCODED,
                "input" => "param=123",
                "expectedResult" => "param%3D123"
            ],
            "it should not sanitize the input if nothing to sanitize" => [
                "filter" => FILTER_SANITIZE_ENCODED,
                "input" => "wontdoanything",
                "expectedResult" => "wontdoanything"
            ]
        ];
    }
}
