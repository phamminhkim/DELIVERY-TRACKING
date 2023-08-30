<?php

namespace App\Services;

use League\Csv\Reader;

class DataRestructureService implements Interfaces\DataRestructureInterface
{
    public function withRegex($raw_data, $pattern)
    {
        preg_match_all($pattern, $raw_data, $matches, PREG_SET_ORDER);

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
}
