<?php

namespace App\Services\Implementations\Restructurers;

use App\Services\Interfaces\DataRestructureInterface;
use App\Utilities\FormatDateUtility;
use App\Utilities\OperatorUtility;

class MergeIndexArrayMappingRestructure implements DataRestructureInterface
{
    public function restructure($data, $options)
    {
        $structure = $options['structure'];
        $output = array();
        foreach ($structure as $structure_key => $array) {
            if (isset($array['key_array'])) {
                // Bỏ qua loại cấu hình theo các key
                continue;
            }
            $output[$structure_key] = array();
            if (isset($array['index'])) {
                foreach ($array['index'] as $array_key => $array_value) {
                    if (!isset($data[$array_value['parent_index']][$array_value['child_index']])) {
                        continue;
                    }
                    $child_value = $data[$array_value['parent_index']][$array_value['child_index']];
                    if ($child_value) {
                        array_push($output[$structure_key], $child_value);
                    }
                }
            }
            $output[$structure_key] = $output[$structure_key] ? implode(" ", $output[$structure_key]) : "";
            // Xử lý replace
            if (isset($array['replace_value'])) {
                $output[$structure_key] = OperatorUtility::replaceValue($output[$structure_key], $array['replace_value']);
            }
            if (isset($array['date_format']) && $output[$structure_key]) {
                if (isset($array['convert_date_format'])) {
                    $output[$structure_key] = FormatDateUtility::formatDate2Date($array['date_format'], $array['convert_date_format'], $output[$structure_key]);
                } else {
                    $output[$structure_key] = FormatDateUtility::formatDate2Date($array['date_format'], 'Y-m-d', $output[$structure_key]);
                }
            }
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
        return $output;
    }
}
