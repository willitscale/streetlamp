<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Validators;

use PHPUnit\Framework\TestCase;
use willitscale\Streetlamp\Attributes\Validators\IntValidator;

class IntValidatorTest extends TestCase
{
    /**
     * @param string $input
     * @param bool $expectedResult
     * @param int|null $min
     * @param int|null $max
     * @return void
     * @dataProvider validateScenarios
     */
    public function testThatValidateCorrectlyValidatesTheInput(
        string $input,
        bool $expectedResult,
        ?int $min = 0,
        ?int $max = PHP_INT_MAX
    ):void {
        $regExpValidator = new IntValidator($min, $max);
        $response = $regExpValidator->validate($input);
        $this->assertEquals($expectedResult, $response);
    }

    /**
     * @param string $input
     * @param int $expectedResult
     * @return void
     * @dataProvider sanitizeScenarios
     */
    public function testThatSanitizeCorrectlySanitizesTheInput(
        string $input,
        int $expectedResult
    ):void {
        $regExpValidator = new IntValidator();
        $response = $regExpValidator->sanitize($input);
        $this->assertEquals($expectedResult, $response);
    }

    /**
     * @return array[]
     */
    public function validateScenarios(): array
    {
        return [
            "it should validate that the value passed is a valid integer" => [
                "input" => "123",
                "expectedResult" => true
            ],
            "it should fail to validate the value passed is a valid integer" => [
                "input" => "123a",
                "expectedResult" => false
            ],
            "it should validate that the value passed is a valid integer between the min and max thresholds" => [
                "input" => "123",
                "expectedResult" => true,
                "min" => 100,
                "max" => 200
            ],
            "it should fail to validate a value passed which is a valid integer but not between the min and max thresholds" => [
                "input" => "123",
                "expectedResult" => false,
                "min" => 124,
                "max" => 125
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function sanitizeScenarios(): array
    {
        return [
            "it should validate that a string sanitizes to the integer equivalent" => [
                "input" => "123",
                "expectedResult" => 123
            ]
        ];
    }
}
