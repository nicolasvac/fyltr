<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Rules;

interface Rule
{
    public function validate(string $key, mixed $value): RuleResult;
}
