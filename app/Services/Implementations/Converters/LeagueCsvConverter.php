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

        // Xử theo từng step mở rộng
        $manual_patterns = $options['manual_patterns'];
        if ($manual_patterns) {
            if (isset($manual_patterns->step1)) {
                // Chỉ lấy các mảng có số lượng phần tử theo config
                if (isset($manual_patterns->step1->array_length)) {
                    $array_length = $manual_patterns->step1->array_length;
                    $collection = $collection->filter(function ($item) use($array_length) {
                        return count($item) === $array_length;
                    });
                }
            }
            if (isset($manual_patterns->step2)) {
                // Xử lý tìm mảng đầu tiên với phần tử bắt đầu với điều kiện
                // Loại bỏ những mảng trước mảng được tìm thấy
                if (isset($manual_patterns->step2->start_array)) {
                    $start_array = $manual_patterns->step2->start_array;
                    $index = $start_array->index;
                    $value = $start_array->value;

                    $collection = $collection->skipUntil(function ($item) use ($index, $value) {
                        return isset($item[$index]) && $item[$index] === $value;
                    });
                }
            }
            if (isset($manual_patterns->step3)) {
                // Loại bỏ những mảng có điều kiện được config
                if (isset($manual_patterns->step3->remove_array)) {
                    $remove_array = $manual_patterns->step3->remove_array;
                    $condition_array = [];
                    foreach ($remove_array as $item) {
                        $index = $item->index;
                        $value = $item->value;
                        array_push($condition_array, $item);
                    }
                    $collection = $collection->reject(function ($item) use ($condition_array) {
                        // Khớp tất cả điều kiện trong $condition_array
                        return collect($condition_array)->every(function ($condition) use ($item) {
                            // Khớp 1 trong các phần tử trong mảng giá trị
                            return collect($condition->value)->contains($item[$condition->index]);
                        });
                    });

                }
            }
        }
        return $collection->toArray();
    }
}
