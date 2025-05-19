<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Exceptions;

/**
 * This exception gets thrown by the validation result when you try to get a value for a key that does not exist.
 */
class ValidationItemNotFoundException extends FyltrException
{
    public function __construct(string $key, string $type = 'Default')
    {
        parent::__construct("The key '$key' was not found for the type $type");
    }
}
