<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Translations;

use Nicolasvac\Fyltr\Rules\IntegerRule;
use Nicolasvac\Fyltr\Rules\RequiredRule;
use Nicolasvac\Fyltr\Rules\RuleWithTranslations;
use Nicolasvac\Fyltr\Rules\SizeRule;

class DefaultTranslationProvider implements TranslationProvider
{
    public function ruleTranslations(RuleWithTranslations $rule): array
    {
        return match ($rule::class) {
            RequiredRule::class => [
                'errors.default' => 'The field :key: is required.'
            ],
            IntegerRule::class => [
                'errors.default' => 'The field :key: must be an integer.',
                'errors.notValidated' => 'The number for the field :key: has not been validated yet.'
            ],
            SizeRule::class => [
                'errors.default' => 'The field :key: is not a sizeable object.',
                'errors.min' => 'The field :key: must be at least :min:.',
                'errors.max' => 'The field :key: must be at most :max:.'
            ]
        };
    }
}
