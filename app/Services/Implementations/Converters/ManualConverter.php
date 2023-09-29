<?php

namespace App\Services\Implementations\Converters;

use App\Models\Business\RegexPattern;
use App\Services\Interfaces\TableConverterInterface;
use League\Csv\Reader;

class ManualConverter implements TableConverterInterface
{
    public function convert($raw_data, $options)
    {
        $manual_patterns = $options['manual_patterns'];

        $regex_patterns = RegexPattern::all();
        $final_pattern = "";
        foreach ($manual_patterns as $manual_pattern) {
            if ($manual_pattern->name == 'custom') {
                $pattern = $manual_pattern->pattern;
            } else {
                $pattern = $regex_patterns->where('name', $manual_pattern->name)->first()->pattern;
            }

            if (isset($manual_pattern->length)) {
                $length = "{" . $manual_pattern->length . "}";
            } else {
                $length = "+";
            }
            $final_pattern .= "(" . $pattern . $length . ")" . "[\\n\\s\\r]*";
        }


        preg_match_all("/\"" . $final_pattern . "\"/", $raw_data, $matches, PREG_SET_ORDER);

        return $matches;
    }
}
