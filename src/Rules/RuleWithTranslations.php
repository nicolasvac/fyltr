<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Rules;

interface RuleWithTranslations extends Rule
{
    /**
     * Sets the messages for the designated rule.
     *
     * @param array<string, string> $messages
     * @return void
     */
    public function setTranslations(array $messages): void;
}
