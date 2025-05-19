<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Rules;

use JsonSerializable;

class RuleResult implements JsonSerializable
{
    public readonly array $errors;

    public function __construct(
        public readonly RuleResultStatus $status,
        array|string                     $errors = [],
    )
    {
        if (is_string($errors)) {
            $this->errors = [$errors];
        } else {
            $this->errors = $errors;
        }
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return [
            'status' => $this->status->value,
            'errors' => $this->errors,
        ];
    }
}
