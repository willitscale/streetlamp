<?php

namespace willitscale\StreetlampTests\Controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class HeadersControllerTest extends ControllerTestCase
{
    #[Test]
    #[DataProvider('headersDataProvider')]
    public function itShouldReturnLowerCaseHeaderValue(
        string $value,
        string $key,
        string $path
    ): void {
        $router = $this->setupRouter(
            'GET',
            $path,
            $this->getTestRoot(),
            $this->getComposerTestFile(),
            null,
            [$key => $value]
        );

        $response = $router->route()->getBody()->getContents();
        $this->assertEquals($value, $response);
    }

    public static function headersDataProvider(): array
    {
        return [
            'it should pass a lower case header with a lower case key' => [
                'value' => 'test',
                'key' => 'lower-case',
                'path' => '/headers/lower-case'
            ],
            'it should pass a lower case header with an upper case key' => [
                'value' => 'test',
                'key' => 'LOWER-CASE',
                'path' => '/headers/lower-case'
            ],
            'it should pass a lower case header with a mixed case key' => [
                'value' => 'test',
                'key' => 'LoWeR-cAsE',
                'path' => '/headers/lower-case'
            ],
            'it should pass a lower case header with a camel case key' => [
                'value' => 'test',
                'key' => 'Lower-Case',
                'path' => '/headers/lower-case'
            ],
            'it should pass a upper case header with an upper case key' => [
                'value' => 'test',
                'key' => 'UPPER-CASE',
                'path' => '/headers/upper-case'
            ],
            'it should pass a upper case header with a lower case key' => [
                'value' => 'test',
                'key' => 'upper-case',
                'path' => '/headers/upper-case'
            ],
            'it should pass a upper case header with a mixed case key' => [
                'value' => 'test',
                'key' => 'UpPeR-cAsE',
                'path' => '/headers/upper-case'
            ],
            'it should pass a upper case header with a camel case key' => [
                'value' => 'test',
                'key' => 'Upper-Case',
                'path' => '/headers/upper-case'
            ],
            'it should pass a mixed case header with an upper case key' => [
                'value' => 'test',
                'key' => 'MIXED-CASE',
                'path' => '/headers/mixed-case'
            ],
            'it should pass a mixed case header with a lower case key' => [
                'value' => 'test',
                'key' => 'mixed-case',
                'path' => '/headers/mixed-case'
            ],
            'it should pass a mixed case header with a mixed case key' => [
                'value' => 'test',
                'key' => 'MiXeD-cAsE',
                'path' => '/headers/mixed-case'
            ],
            'it should pass a mixed case header with a camel case key' => [
                'value' => 'test',
                'key' => 'Mixed-Case',
                'path' => '/headers/mixed-case'
            ],
            'it should pass a camel case header with an upper case key' => [
                'value' => 'test',
                'key' => 'CAMEL-CASE',
                'path' => '/headers/camel-case'
            ],
            'it should pass a camel case header with a lower case key' => [
                'value' => 'test',
                'key' => 'camel-case',
                'path' => '/headers/camel-case'
            ],
            'it should pass a camel case header with a mixed case key' => [
                'value' => 'test',
                'key' => 'CaMeL-cAsE',
                'path' => '/headers/camel-case'
            ],
            'it should pass a camel case header with a camel case key' => [
                'value' => 'test',
                'key' => 'Camel-Case',
                'path' => '/headers/camel-case'
            ],
        ];
    }
}
