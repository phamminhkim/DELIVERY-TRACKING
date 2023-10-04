<?php

namespace App\Enums\Ai\Convert;

use App\Enums\Abs\Enum;

abstract class ConvertMethod extends Enum
{
    const REGEXMATCH = 'regexmatch';
    const REGEXSPLIT = 'regexsplit';
    const LEAGUECSV = 'leaguecsv';
    const MANUAL = 'manual';
}
