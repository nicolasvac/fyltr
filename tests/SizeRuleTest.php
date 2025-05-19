<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr;

use Nicolasvac\Fyltr\Rules\Rules;
use PHPUnit\Framework\TestCase;

class SizeRuleTest extends TestCase
{
    public function testNumber(): void
    {
        $fyltr = new Validator(
            inputs: [
                'v' => 1,
            ],
            validators: [
                'v' => [Rules::size(min: 0, max: 2)]
            ]
        );

        $this->assertTrue($fyltr->validate()->successful(), 'The validation should be successful.');
    }

    public function testString(): void
    {
        $fyltr = new Validator(
            inputs: [
                'v' => 'hello',
            ],
            validators: [
                'v' => [Rules::size(min: 2, max: 5)]
            ]
        );

        $this->assertTrue($fyltr->validate()->successful(), 'The validation should be successful.');
    }
}
