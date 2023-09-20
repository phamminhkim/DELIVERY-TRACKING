<?php

namespace App\Services\Implementations\Converters;

use App\Models\Business\RegexPattern;
use App\Services\Interfaces\TableConverterInterface;
use League\Csv\Reader;

class RegexSplitConverter implements TableConverterInterface
{
    public function convert($raw_data, $options)
    {
        $pattern = $options['regex_pattern'];
        $matches = preg_split($pattern, $raw_data);

        return $matches;
    }
}
