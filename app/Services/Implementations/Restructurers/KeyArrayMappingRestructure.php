<?php

namespace App\Services\Implementations\Restructurers;

use App\Services\Interfaces\DataRestructureInterface;
use App\Utilities\OperatorUtility;

class KeyArrayMappingRestructure implements DataRestructureInterface
{
    public function restructure($data, $options)
    {
        try {
            $structure = $options['structure'];
            $collection = collect([]);
            foreach ($data as $match) {
                $output = [];
                foreach ($structure as $key => $value_item) {
                    if (isset($value_item['value'])) {
                        $output[$key] = $match[$value_item['value']] ?
                        $match[$value_item['value']] :
                        (isset($value_item['default']) ? $value_item['default'] :
                        $match[$value_item['value']]);
                    } else if (isset($value_item['value_1'])
                        && isset($value_item['value_2'])
                        && isset($value_item['operator'])) {
                        // Lấy giá trị 1 và giá trị 2, thực hiện phép toán
                        $value_1 = $match[$value_item['value_1']] ?
                        $match[$value_item['value_1']] :
                        (isset($value_item['default']) ? $value_item['default'] :
                        $match[$value_item['value_1']]);

                        $value_2 = $match[$value_item['value_2']] ?
                        $match[$value_item['value_2']] :
                        (isset($value_item['default']) ? $value_item['default'] :
                        $match[$value_item['value_2']]);

                        $operator = OperatorUtility::getOperator($value_item['operator']);
                        $output[$key] = OperatorUtility::$operator($value_1, $value_2);
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
        } catch (\Throwable $th) {
            dd($th);
            return null;
        }
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
