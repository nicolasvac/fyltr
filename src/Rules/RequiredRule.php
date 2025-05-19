<?php

namespace Nicolasvac\Fyltr\Rules;

class RequiredRule implements RuleWithTranslations
{

    private readonly string $errorMessage;

    public function __construct()
    {
    }

    public function validate(string $key, mixed $value): RuleResult
    {
        if (isset($value) && $value !== '') {
            return new RuleResult(status: RuleResultStatus::Successful);
        } else {
            return new RuleResult(
                status: RuleResultStatus::FailedAndStopped,
                errors: str_replace(':key:', $key, $this->errorMessage),
            );
        }
    }

    public function getErrorMessage(string $key): string
    {
        if (str_contains($this->errorMessage, ':key:')) {
            return ;
        }

        return empty($this->errorMessage) ?: "The field '$key' is required.";
    }

    public function setErrorMessage(string $message): void
    {
        $this->errorMessage = $message;
    }
}
