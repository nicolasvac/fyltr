<?php

namespace Nicolasvac\Fyltr\Exceptions;

class ValidationKeyFoundMultipleTimes extends FyltrException
{
    public function __construct(string $key)
    {
        parent::__construct("The key '$key' was found multiple times inside the validation inputs.");
    }
}
