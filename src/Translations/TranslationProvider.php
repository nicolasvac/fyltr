<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Translations;

use Nicolasvac\Fyltr\Rules\RuleWithTranslations;

interface TranslationProvider
{
    public function ruleTranslations(RuleWithTranslations $rule): array;
}
