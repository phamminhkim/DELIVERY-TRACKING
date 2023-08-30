<?php

namespace App\Services;

use League\Csv\Reader;

class DataRestructureService implements Interfaces\DataRestructureInterface
{
    public function withRegex($raw_data, $pattern, $structure)
    {
        preg_match_all($pattern, $raw_data, $matches, PREG_SET_ORDER);

        $collection = collect([]);
        foreach ($matches as $match) {
            $output = [];
            foreach ($structure as $key => $index) {
                $output[$key] = $match[$index];
            }
            $collection->push($output);
        }

        return $collection;
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
