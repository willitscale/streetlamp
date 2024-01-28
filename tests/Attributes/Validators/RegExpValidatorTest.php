<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Validators;

use PHPUnit\Framework\TestCase;
use willitscale\Streetlamp\Attributes\Validators\RegExpValidator;

class RegExpValidatorTest extends TestCase
{
    /**
     * @param string $pattern
     * @param string $input
     * @param bool $expectedResult
     * @return void
     * @dataProvider validateScenarios
     */
    public function testThatValidateCorrectlyValidatesTheInput(
        string $pattern,
        string $input,
        bool $expectedResult
    ): void {
        $regExpValidator = new RegExpValidator($pattern);
        $response = $regExpValidator->validate($input);
        $this->assertEquals($expectedResult, $response);
    }

    /**
     * @param string $pattern
     * @param string $replace
     * @param string $input
     * @param string $expectedResult
     * @return void
     * @dataProvider sanitizeScenarios
     */
    public function testThatSanitizeCorrectlySanitizesTheInput(
        string $pattern,
        string $replace,
        string $input,
        string $expectedResult
    ): void {
        $regExpValidator = new RegExpValidator($pattern, $replace);
        $response = $regExpValidator->sanitize($input);
        $this->assertEquals($expectedResult, $response);
    }

    /**
     * @return array[]
     */
    public function validateScenarios(): array
    {
        return [
            "it should validate that a date string matches correctly" => [
                "pattern" => "/\d{4}-\d{2}-\d{2}/",
                "input" => "2020-01-01",
                "expectedResult" => true
            ],
            "it should fail to validate a date string that's incorrectly formatted" => [
                "pattern" => "/\d{4}-\d{2}-\d{2}/",
                "input" => "not a valid date",
                "expectedResult" => false
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function sanitizeScenarios(): array
    {
        return [
            "it should sanitize the input by replacing the matched values" => [
                "pattern" => "/\d{4}/",
                "replace" => "2024",
                "input" => "2020-01-01",
                "expectedResult" => "2024-01-01"
            ],
            "it should not sanitize the input if the pattern doesn't match" => [
                "pattern" => "/[a-z]/i",
                "replace" => "Won't replace with me",
                "input" => "2020-01-01",
                "expectedResult" => "2020-01-01"
            ]
        ];
    }
}
