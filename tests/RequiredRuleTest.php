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
                'name' => 'Nicolas',
            ],
            validators: [
                'name' => [Rules::required()]
            ]
        );

        $this->assertTrue($fyltr->validate()->successful(), 'The validation should be successful.');
    }

    public function testEmptyString(): void
    {
        $fyltr = new Validator(
            inputs: [
                'name' => '',
            ],
            validators: [
                'name' => [Rules::required()]
            ]
        );

        $this->assertFalse($fyltr->validate()->successful(), 'The validation should be unsuccessful.');
    }

    public function testNullValue(): void
    {
        $fyltr = new Validator(
            inputs: [
                'name' => null,
            ],
            validators: [
                'name' => [Rules::required()]
            ]
        );

        $this->assertFalse($fyltr->validate()->successful(), 'The validation should be unsuccessful.');
    }

    public function testNumber(): void
    {
        $fyltr = new Validator(
            inputs: [
                'name' => 1,
            ],
            validators: [
                'name' => [Rules::required()]
            ]
        );

        $this->assertTrue($fyltr->validate()->successful(), 'The validation should be successful.');
    }

    public function testFalsy(): void
    {
        $fyltr = new Validator(
            inputs: [
                'name' => false,
            ],
            validators: [
                'name' => [Rules::required()]
            ]
        );

        $this->assertTrue($fyltr->validate()->successful(), 'The validation should be successful.');
    }
}
