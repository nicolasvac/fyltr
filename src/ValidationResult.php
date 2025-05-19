<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr;

use DateTime;
use DateTimeInterface;
use JsonSerializable;

class ValidationResult implements JsonSerializable
{
    /**
     * @var string[] A list of errors connected to this result.
     */
    private readonly array $errors;

    /**
     * @var bool Indicates if the validation has been successful.
     */
    private readonly bool $successful;

    /**
     * @var array{data: array{0: string, 1: array{0: int, 1: mixed}}, files: array{0: string, 1: string}}
     */
    private readonly array $dataBag;

    public function __construct(bool $successful, array $errors, array $dataBag)
    {
        $this->successful = $successful;
        $this->errors = $errors;
        $this->dataBag = $dataBag;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function errorsString(string $separator = "\n"): string
    {
        return implode(separator: $separator, array: $this->errors);
    }

    public function successful(): bool
    {
        return $this->successful;
    }

    public function get(string $key): mixed
    {
        if (isset($this->dataBag['data'][$key][ValidationResultDataTypes::Number->value])) {
            return $this->dataBag['data'][$key][ValidationResultDataTypes::Number->value];
        }

        if (isset($this->dataBag['data'][$key][ValidationResultDataTypes::Boolean->value])) {
            return $this->dataBag['data'][$key][ValidationResultDataTypes::Boolean->value];
        }

        return $this->dataBag['data'][$key][ValidationResultDataTypes::Raw->value] ?? null;
    }

    public function has(string $key): bool
    {
        return $this->get(key: $key) !== null;
    }

    public function date(string $key): DateTime|null
    {
        return $this->dataBag['data'][$key][ValidationResultDataTypes::DateTime->value] ?? null;
    }

    public function dateFormat(string $key, string $format = DateTimeInterface::ATOM): string|null
    {
        return $this->date(key: $key)?->format(format: $format) ?? null;
    }

    public function file(string $key): string|null
    {
        return $this->dataBag['files'][$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return [
            'success' => $this->successful(),
            'errors' => $this->errors(),
        ];
    }

    public function __get(string $name)
    {
        return $this->get(key: $name);
    }

    public function __isset(string $name): bool
    {
        return $this->has(key: $name);
    }
}
