<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Rules;

class SizeRule implements RuleWithTranslations
{
    /** @var array<string, string> */
    private array $messages;

    public function __construct()
    {
    }

    public function validate(string $key, mixed $value, array $args = []): RuleResult
    {
        if (!isset($value) || $value === '') {
            return new RuleResult(status: RuleResultStatus::Successful);
        }

        $minValue = $args[0] ?? null;
        $maxValue = $args[1] ?? null;

        return match (gettype(value: $value)) {
            'integer', 'double' => $this->validateNumber(key: $key, value: $value, minValue: $minValue, maxValue: $maxValue),
            'array', 'object' => $this->validateCountable(key: $key, value: $value, minSize: $minValue, maxSize: $maxValue),
            'string' => $this->validateString(key: $key, value: $value, minLength: $minValue, maxLength: $maxValue),
            default => $this->createFailedResult(key: $key)
        };
    }

    private function validateNumber(string $key, int|float $value, ?float $minValue, ?float $maxValue): RuleResult
    {
        $numericValue = (float)$value;

        if ($minValue !== null && $numericValue < $minValue) {
            return $this->createMinValidationError(key: $key, min: $minValue);
        }

        if ($maxValue !== null && $numericValue > $maxValue) {
            return $this->createMaxValidationError(key: $key, max: $maxValue);
        }

        return new RuleResult(status: RuleResultStatus::Successful);
    }

    private function validateCountable(string $key, mixed $value, ?int $minSize, ?int $maxSize): RuleResult
    {
        if (!is_countable(value: $value)) {
            return $this->createFailedResult(key: $key);
        }

        $size = count(value: $value);

        if ($minSize !== null && $size < $minSize) {
            return $this->createMinValidationError(key: $key, min: $minSize);
        }

        if ($maxSize !== null && $size > $maxSize) {
            return $this->createMaxValidationError(key: $key, max: $maxSize);
        }

        return new RuleResult(status: RuleResultStatus::Successful);
    }

    private function validateString(string $key, string $value, ?int $minLength, ?int $maxLength): RuleResult
    {
        $length = strlen(string: $value);

        if ($minLength !== null && $length < $minLength) {
            return $this->createMinValidationError(key: $key, min: $minLength);
        }

        if ($maxLength !== null && $length > $maxLength) {
            return $this->createMaxValidationError(key: $key, max: $maxLength);
        }

        return new RuleResult(status: RuleResultStatus::Successful);
    }

    private function createFailedResult(string $key): RuleResult
    {
        return new RuleResult(
            status: RuleResultStatus::Failed,
            errors: [str_replace(search: ':key:', replace: $key, subject: $this->messages['errors.default'])]
        );
    }

    private function createMinValidationError(string $key, int|float $min): RuleResult
    {
        return new RuleResult(
            status: RuleResultStatus::Failed,
            errors: [$this->getMinErrorMessage(key: $key, minValue: $min)]
        );
    }

    private function createMaxValidationError(string $key, int|float $max): RuleResult
    {
        return new RuleResult(
            status: RuleResultStatus::Failed,
            errors: [$this->getMaxErrorMessage(key: $key, maxValue: $max)]
        );
    }

    /** @inheritDoc */
    public function setTranslations(array $messages): void
    {
        $this->messages = $messages;
    }

    private function getMinErrorMessage(string $key, int|float $minValue): string
    {
        return str_replace(
            search: [':key:', ':minValue:'],
            replace: [$key, strval(value: $minValue)],
            subject: $this->messages['errors.min'],
        );
    }

    private function getMaxErrorMessage(string $key, int|float $maxValue): string
    {
        return str_replace(
            search: [':key:', ':maxValue:'],
            replace: [$key, strval(value: $maxValue)],
            subject: $this->messages['errors.max'],
        );
    }
}
