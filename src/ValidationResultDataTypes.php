<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr;

enum ValidationResultDataTypes: int
{
    case Raw = 1;
    case DateTime = 2;
    case Number = 3;
    case Boolean = 4;
}
