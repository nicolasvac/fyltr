<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Rules;

use Nicolasvac\Fyltr\Exceptions\RuleInternalException;

class IntegerRule implements RuleWithTranslations, RuleReturnsNumber
{

    /** @var array<string, string> */
    private array $messages;

    private int|null $number = null;

    public function __construct()
    {
    }

    public function validate(string $key, mixed $value, array $args = []): RuleResult
    {
        if (!isset($value) || $value === '') {
            return new RuleResult(status: RuleResultStatus::Successful);
        }

        $status = RuleResultStatus::Failed;

        switch (gettype($value)) {
            case 'string':
                $modifiedValue = clone $value;

                if (strlen($modifiedValue) > 1 && $modifiedValue[0] === '-') {
                    $modifiedValue = substr($modifiedValue, 1);
                }

                if (ctype_digit($modifiedValue)) {
                    $status = RuleResultStatus::Successful;
                    $this->number = intval($value);
                }

                break;
            case 'integer':
                $status = RuleResultStatus::Successful;
                $this->number = intval($value);
                break;
        }

        return new RuleResult(
            status: $status,
            errors: $status === RuleResultStatus::Successful
                ? []
                : str_replace(':key:', $key, $this->messages['errors.default']),
        );
    }

    /** @inheritDoc */
    public function setTranslations(array $messages): void
    {
        $this->messages = $messages;
    }

    /** @inheritDoc */
    public function getValidatedNumber(): int|float
    {
        if ($this->number === null) {
            throw new RuleInternalException($this->messages['errors.notValidated']);
        }

        return $this->number;
    }
}
