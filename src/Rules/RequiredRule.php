<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Rules;

class RequiredRule implements RuleWithTranslations
{
    /** @var array<string, string> */
    private array $messages;

    public function __construct()
    {
    }

    public function validate(string $key, mixed $value, array $args = []): RuleResult
    {
        if (isset($value) && $value !== '') {
            return new RuleResult(status: RuleResultStatus::Successful);
        } else {
            return new RuleResult(
                status: RuleResultStatus::FailedAndStopped,
                errors: str_replace(':key:', $key, $this->messages['errors.default']),
            );
        }
    }

    /** @inheritDoc */
    public function setTranslations(array $messages): void
    {
        $this->messages = $messages;
    }
}
