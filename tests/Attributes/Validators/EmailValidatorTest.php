<?php

declare(strict_types=1);

namespace willitscale\StreetlampTests\Attributes\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use willitscale\Streetlamp\Attributes\Validators\EmailValidator;

class EmailValidatorTest extends TestCase
{
    #[DataProvider('validateScenarios')]
    public function testThatValidateCorrectlyValidatesTheInput(
        string $input,
        bool $expectedResult
    ): void {
        $regExpValidator = new EmailValidator();
        $response = $regExpValidator->validate($input);
        $this->assertEquals($expectedResult, $response);
    }

    public static function validateScenarios(): array
    {
        return [
            "it should validate that the input is a valid email address" => [
                "input" => "test@example.com",
                "expectedResult" => true
            ],
            "it should validation should fail when a string does not contain a valid email address" => [
                "input" => "test@example",
                "expectedResult" => false
            ]
        ];
    }
}
