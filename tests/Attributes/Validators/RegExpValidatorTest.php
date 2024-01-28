<?php

namespace willitscale\StreetlampTests\Attributes\Validators;

use PHPUnit\Framework\TestCase;
use willitscale\Streetlamp\Attributes\Validators\RegExpValidator;

class RegExpValidatorTest extends TestCase
{
    /**
     * @dataProvider validateScenarios
     * @return void
     */
    public function testThatValidateWorksCorrectly(string $pattern, string $input, bool $expectedResult)
    {
        $regExpValidator = new RegExpValidator($pattern);
        $response = $regExpValidator->validate($input);
        $this->assertEquals($expectedResult, $response);
    }

    public function validateScenarios(): array
    {
        return [
            "it should validate that a date string matches correctly" => [
                'pattern' => "/\d{4}-\d{2}-\d{2}/",
                'input' => "2020-01-01",
                'expectedResult' => true
            ],
            "it should fail to validate a date string that's incorrectly formatted" => [
                'pattern' => "/\d{4}-\d{2}-\d{2}/",
                'input' => "not a valid date",
                'expectedResult' => false
            ]
        ];
    }
}
