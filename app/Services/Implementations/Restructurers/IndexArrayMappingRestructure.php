<?php

namespace App\Services\Implementations\Restructurers;

use App\Services\Interfaces\DataRestructureInterface;

class IndexArrayMappingRestructure implements DataRestructureInterface
{
    public function restructure($data, $options)
    {
        $structure = $options['structure'];
        $collection = collect([]);
        foreach ($data as $match) {
            $output = [];
            foreach ($structure as $key => $value_item) {
                if ($value_item['value']) {
                    $output[$key] = $match[$value_item['value']] ?
                    $match[$value_item['value']] :
                    (isset($value_item['default']) ? $value_item['default'] :
                    $match[$value_item['value']]);
                } else {
                    $output[$key] = $value_item['default'];
                }
            }
            // Check trường bắt buộc mà không có giá trị thì skip row
            if ($this->isValidArrayValue($output, $structure)) {
                $collection->push($output);
            }
        }
        return $collection->toArray();
    }

    public function isValidArrayValue($datas, $structure)
    {
        foreach ($structure as $key => $data) {
            if ($data['require']) {
                if (!$datas[$key]) {
                    return false;
                }
            }
        }
        return true;
    }
}
