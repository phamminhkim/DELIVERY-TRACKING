<?php

namespace App\Services\Implementations\Restructurers;

use App\Services\Interfaces\DataRestructureInterface;
use App\Utilities\OperatorUtility;
use App\Utilities\FormatDateUtility;

class KeyArrayMappingRestructure implements DataRestructureInterface
{
    public function restructure($data, $options)
    {
        $structure = $options['structure'];
        $has_default_decimal_separator = isset($options['decimal_separator']);
        $default_decimal_separator = $has_default_decimal_separator ? $options['decimal_separator'] : null;
        $default_thousand_separator = $default_decimal_separator === ',' ? '.' : ',';

        $collection = collect([]);
        $skip_item = false;

        foreach ($data as $match) {
            $output = [];
            $skip_item = false;

            // Chuyển đổi object thành array một lần duy nhất
            if (is_object($match)) {
                $match = json_decode(json_encode($match), true);
            }

            foreach ($structure as $key => $value_item) {
                if (isset($value_item['key_array'])) {
                    // Bỏ qua loại cấu hình theo các key
                    continue;
                }

                $value = null;
                if (isset($value_item['value'])) {
                    if (!array_key_exists($value_item['value'], $match)) {
                        // Tìm không thấy key trong mảng data thì bỏ qua
                        $skip_item = true;
                        break;
                    }
                    $value = $match[$value_item['value']] ?: (isset($value_item['default']) ? $value_item['default'] : $match[$value_item['value']]);
                } elseif (isset($value_item['value_1']) && isset($value_item['value_2']) && isset($value_item['operator'])) {
                    if (!array_key_exists($value_item['value_1'], $match) || !array_key_exists($value_item['value_2'], $match)) {
                        // Tìm không thấy key trong mảng data thì bỏ qua
                        $skip_item = true;
                        break;
                    }
                    $value_1 = $match[$value_item['value_1']] ?: (isset($value_item['default']) ? $value_item['default'] : $match[$value_item['value_1']]);
                    $value_2 = $match[$value_item['value_2']] ?: (isset($value_item['default']) ? $value_item['default'] : $match[$value_item['value_2']]);

                    // Kiểm tra giá trị số
                    if (is_numeric($value_1) && is_numeric($value_2)) {
                        $operator = OperatorUtility::getOperator($value_item['operator']);
                        $value = OperatorUtility::$operator($value_1, $value_2);
                    } else {
                        $value = "";
                    }
                } else {
                    $value = $value_item['default'];
                }

                // Thay thế dấu phân cách thập phân và phân cách hàng nghìn
                if (isset($value_item['decimal_separator']) && isset($value_item['thousand_separator'])) {
                    $value = $has_default_decimal_separator
                        ? OperatorUtility::replaceSeparator($value, $default_decimal_separator, $default_thousand_separator)
                        : OperatorUtility::replaceSeparator($value, $value_item['decimal_separator'], $value_item['thousand_separator']);
                }

                if (isset($value_item['regex_match'])) {
                    if (isset($value_item['regex_match_index'])) {
                        $value = OperatorUtility::regexMatch($value, $value_item['regex_match'], $value_item['regex_match_index']);
                    } else {
                        $value = OperatorUtility::regexMatch($value, $value_item['regex_match']);
                    }
                }

                // Xử lý replace
                if (isset($value_item['replace_value'])) {
                    $value = OperatorUtility::replaceValue($value, $value_item['replace_value']);
                }

                // Xử lý convert string sang price
                if (isset($value_item['is_convert_to_price']) && $value_item['is_convert_to_price'] == true) {
                    $value = floatval($value);
                }

                // Xử lý format ngày
                if (isset($value_item['date_format']) && $value) {
                    $value = isset($value_item['convert_date_format'])
                        ? FormatDateUtility::formatDate2Date($value_item['date_format'], $value_item['convert_date_format'], $value)
                        : FormatDateUtility::formatDate2Date($value_item['date_format'], 'Y-m-d', $value);
                }

                $output[$key] = $value;
            }

            if ($skip_item) {
                // Tìm không thấy key trong mảng data thì bỏ qua
                continue;
            }

            // Xử lý loại cấu hình theo các key
            foreach ($structure as $structure_key => $array) {
                if (isset($array['key_array'])) {
                    if (isset($array['join_after_add_customer']) && $array['join_after_add_customer'] == true) {
                        // Lưu lại cấu trúc key để xử lý sau khi add thông tin khách hàng
                        $output[$structure_key] = $array;
                    } else {
                        // Thực hiện join các key
                        $key_array = $array['key_array'];
                        $separator = $array['separator'];
                        $value_array = [];
                        foreach ($key_array as $key) {
                            $value_array[] = $output[$key];
                        }
                        $output[$structure_key] = implode($separator, $value_array);
                    }
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
