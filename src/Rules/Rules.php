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

    public static function int(): array
    {
        return [
            'class' => IntegerRule::class,
            'args' => [],
        ];
    }

    public static function size(int|float|null $min = null, int|float|null $max = null): array
    {
        return [
            'class' => SizeRule::class,
            'args' => [$min, $max],
        ];
    }
}
