<?php

namespace App\Enums\File;

use App\Enums\Abs\Enum;

abstract class FileStatuses extends Enum
{
    const NEW = 10;
    const PROCESSING = 20;
    const SUCCESS = 30;
    const ERROR = 40;
    const RECONVERT = 50;
    const CONVERTED = 60;
    const WAITING_SYNC = 70;
}
