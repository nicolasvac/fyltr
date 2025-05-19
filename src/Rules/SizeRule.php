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

        return match (gettype($value)) {
            'integer', 'double' => $this->validateNumber($key, $value, $minValue, $maxValue),
            'array', 'object' => $this->validateCollection($key, $value, $minValue, $maxValue),
            'string' => $this->validateString($key, $value, $minValue, $maxValue),
            default => $this->createFailedResult($key)
        };
    }

    private function validateNumber(string $key, int|float $value, ?float $min, ?float $max): RuleResult
    {
        $numericValue = (float)$value;

        if ($min !== null && $numericValue < $min) {
            return $this->createMinValidationError($key, $min);
        }

        if ($max !== null && $numericValue > $max) {
            return $this->createMaxValidationError($key, $max);
        }

        return new RuleResult(status: RuleResultStatus::Successful);
    }

    private function validateCollection(string $key, mixed $value, ?int $min, ?int $max): RuleResult
    {
        if (!is_countable($value)) {
            return $this->createFailedResult($key);
        }

        $size = count($value);

        if ($min !== null && $size < $min) {
            return $this->createMinValidationError($key, $min);
        }

        if ($max !== null && $size > $max) {
            return $this->createMaxValidationError($key, $max);
        }

        return new RuleResult(status: RuleResultStatus::Successful);
    }

    private function validateString(string $key, string $value, ?int $min, ?int $max): RuleResult
    {
        $length = strlen($value);

        if ($min !== null && $length < $min) {
            return $this->createMinValidationError($key, $min);
        }

        if ($max !== null && $length > $max) {
            return $this->createMaxValidationError($key, $max);
        }

        return new RuleResult(status: RuleResultStatus::Successful);
    }

    private function createFailedResult(string $key): RuleResult
    {
        return new RuleResult(
            status: RuleResultStatus::Failed,
            errors: [$this->messages['errors.default']]
        );
    }

    private function createMinValidationError(string $key, int|float $min): RuleResult
    {
        return new RuleResult(
            status: RuleResultStatus::Failed,
            errors: [$this->getMinErrorMessage($key, $min)]
        );
    }

    private function createMaxValidationError(string $key, int|float $max): RuleResult
    {
        return new RuleResult(
            status: RuleResultStatus::Failed,
            errors: [$this->getMaxErrorMessage($key, $max)]
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
