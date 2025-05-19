<?php

namespace Nicolasvac\Fyltr\Rules;

enum RuleResultStatus: int
{
    case Failed = 1;
    case Successful = 2;
    case FailedAndStopped = 3;
}
