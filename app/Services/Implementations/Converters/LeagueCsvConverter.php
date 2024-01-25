<?php

namespace App\Services\Implementations\Converters;

use App\Models\Business\RegexPattern;
use App\Services\Interfaces\TableConverterInterface;
use League\Csv\Reader;

class LeagueCsvConverter implements TableConverterInterface
{
    public function convert($raw_data, $options)
    {
        $csv = Reader::createFromString($raw_data);
        if (!$options['is_without_header']) {
            $csv->setHeaderOffset(0); // Set the CSV header offset
        }

        $records = $csv->getRecords();

        $collection = collect([]);
        foreach ($records as $record) {
            $collection->push($record);
        }
        return $collection->toArray();
    }
}
