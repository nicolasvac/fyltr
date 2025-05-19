<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr;

use Nicolasvac\Fyltr\Rules\Rules;
use PHPUnit\Framework\TestCase;

final class RequiredRuleTest extends TestCase
{
    public function testBasic(): void
    {
        $fyltr = new Validator(
            inputs: [
                'v' => 'Nicolas',
            ],
            validators: [
                'v' => [Rules::required()]
            ]
        );

        $this->assertTrue($fyltr->validate()->successful(), 'The validation should be successful.');
    }

    public function testEmptyString(): void
    {
        $fyltr = new Validator(
            inputs: [
                'v' => '',
            ],
            validators: [
                'v' => [Rules::required()]
            ]
        );

        $this->assertFalse($fyltr->validate()->successful(), 'The validation should be unsuccessful.');
    }

    public function testNullValue(): void
    {
        $fyltr = new Validator(
            inputs: [
                'v' => null,
            ],
            validators: [
                'v' => [Rules::required()]
            ]
        );

        $this->assertFalse($fyltr->validate()->successful(), 'The validation should be unsuccessful.');
    }

    public function testNumber(): void
    {
        $fyltr = new Validator(
            inputs: [
                'v' => 1,
            ],
            validators: [
                'v' => [Rules::required()]
            ]
        );

        $this->assertTrue($fyltr->validate()->successful(), 'The validation should be successful.');
    }

    public function testFalsy(): void
    {
        $fyltr = new Validator(
            inputs: [
                'v' => false,
            ],
            validators: [
                'v' => [Rules::required()]
            ]
        );

        $this->assertTrue($fyltr->validate()->successful(), 'The validation should be successful.');
    }
}
