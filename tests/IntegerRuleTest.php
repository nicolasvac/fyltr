<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr;

use Nicolasvac\Fyltr\Rules\Rules;
use PHPUnit\Framework\TestCase;

final class IntegerRuleTest extends TestCase
{
    public function testBasic(): void
    {
        $fyltr = new Validator(
            inputs: [
                'v' => 1,
            ],
            validators: [
                'v' => [Rules::int()]
            ]
        );

        $this->assertTrue($fyltr->validate()->successful(), 'The validation should be successful.');
    }
}
