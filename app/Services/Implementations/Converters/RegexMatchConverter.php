<?php

namespace App\Services\Implementations\Converters;

use App\Models\Business\RegexPattern;
use App\Services\Interfaces\TableConverterInterface;
use League\Csv\Reader;

class RegexMatchConverter implements TableConverterInterface
{
    public function convert($raw_data, $options)
    {
        $pattern = $options['regex_pattern'];
        preg_match_all($pattern, $raw_data, $matches, PREG_SET_ORDER);

        return $matches;
    }
}
