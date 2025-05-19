<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Rules;

interface RuleWithTranslations extends Rule
{
    public function setErrorMessage(string $message): void;
}
