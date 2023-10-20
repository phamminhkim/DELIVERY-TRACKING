<?php

namespace App\Enums\Ai\Error;

use App\Enums\Abs\Enum;

abstract class ExtractErrors extends Enum
{
    const EXTRACT_ERROR = 10;
    const CONVERT_ERROR = 20;
    const RESTRUCTURE_ERROR = 30;
    const NOT_FOUND_CUSTOMER_MATERIAL = 40;
    const NOT_FOUND_SAP_MATERIAL = 50;
}
