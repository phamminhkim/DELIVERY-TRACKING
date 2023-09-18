<?php

namespace App\Services;

use App\Models\Business\RegexPattern;
use League\Csv\Reader;

class TableConverterService implements Interfaces\TableConverterInterface
{
    public function withRegexMatch($raw_data, $pattern)
    {
        preg_match_all($pattern, $raw_data, $matches, PREG_SET_ORDER);

        return $matches;
    }
    public function withRegexSplit($raw_data, $pattern)
    {
        $matches = preg_split($pattern, $raw_data);

        return $matches;
    }
    public function withLeagueCsv($raw_data)
    {
        $csv = Reader::createFromString($raw_data);
        $csv->setHeaderOffset(0); // Set the CSV header offset

        $records = $csv->getRecords();

        $collection = collect([]);
        foreach ($records as $record) {
            $collection->push($record);
        }
        return $collection;
    }

    public function withManualPattern($raw_data, $manual_patterns)
    {
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
