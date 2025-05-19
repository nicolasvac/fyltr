<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Translations;

use Nicolasvac\Fyltr\Rules\RequiredRule;
use Nicolasvac\Fyltr\Rules\Rule;

class DefaultTranslationProvider implements TranslationProvider
{
    public function ruleErrorMessage(Rule $rule): string
    {
        return match ($rule::class) {
            RequiredRule::class => 'The field :key: is required.',
        };
    }
}
