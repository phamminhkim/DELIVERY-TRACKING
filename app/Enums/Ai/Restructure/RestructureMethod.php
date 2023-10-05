<?php

namespace App\Enums\Ai\Restructure;

use App\Enums\Abs\Enum;

abstract class RestructureMethod extends Enum
{
    const INDEXARRAYMAPPING = 'arraymappingbyindex';
    const KEYARRAYMAPPING = 'arraymappingbykey';
}
