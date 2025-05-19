<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr\Rules;

class Rules
{
    public static function required(): array
    {
        return [
            'class' => RequiredRule::class,
            'args' => [],
        ];
    }
}
