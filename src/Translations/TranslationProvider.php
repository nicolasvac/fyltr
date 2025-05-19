<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Translations;

use Nicolasvac\Fyltr\Rules\Rule;

interface TranslationProvider
{
    public function ruleErrorMessage(Rule $rule): string;
}
