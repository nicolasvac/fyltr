<?php

namespace Nicolasvac\Fyltr\Rules;

use Nicolasvac\Fyltr\Exceptions\RuleInternalException;

/**
 * Every rule that implements this interface will have automatic casts to number data types
 * in php for easier handling.
 */
interface RuleReturnsNumber
{
    /**
     * @throws RuleInternalException
     */
    public function getValidatedNumber(): int|float;
}
