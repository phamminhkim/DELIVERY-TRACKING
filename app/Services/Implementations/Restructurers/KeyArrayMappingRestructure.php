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
            $skip_item = false;
            foreach ($data as $match) {
                $output = [];
                foreach ($structure as $key => $value_item) {
                    if (isset($value_item['value'])) {
                        if (!isset($match[$value_item['value']])) {
                            // Tìm không thấy key trong mảng data thì bỏ qua
                            $skip_item = true;
                            continue;
                        }
                        $output[$key] = $match[$value_item['value']] ?
                        $match[$value_item['value']] :
                        (isset($value_item['default']) ? $value_item['default'] :
                        $match[$value_item['value']]);
                    } else if (isset($value_item['value_1'])
                        && isset($value_item['value_2'])
                        && isset($value_item['operator'])) {
                        if (!isset($match[$value_item['value1']]) || !isset($match[$value_item['value2']])) {
                            // Tìm không thấy key trong mảng data thì bỏ qua
                            $skip_item = true;
                            continue;
                        }
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
                    // Thay thế dấu phân cách thập phân và phân cách hàng nghìn
                    if (isset($value_item['decimal_separator']) && isset($value_item['thousand_separator'])) {
                        $output[$key] = OperatorUtility::replaceSeparator($output[$key], $value_item['decimal_separator'], $value_item['thousand_separator']);
                    }
                    if (isset($value_item['regex_match'])) {
                        $output[$key] = OperatorUtility::regexMatch($output[$key], $value_item['regex_match']);
                    }
                }
                if ($skip_item) {
                    // Tìm không thấy key trong mảng data thì bỏ qua
                    $skip_item = false;
                    continue;
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
