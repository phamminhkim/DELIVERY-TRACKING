<?php

namespace App\Services\Implementations\Restructurers;

use App\Services\Interfaces\DataRestructureInterface;
use App\Utilities\FormatDateUtility;
use App\Utilities\OperatorUtility;

class SearchTextArrayMappingRestructure implements DataRestructureInterface
{
    public function restructure($data, $options)
    {
        $structure = $options['structure'];
        $data_str = "";
        $output = array();
        foreach ($data as $data_arr) {
            $data_str .= implode(' ', $data_arr);
        }
        foreach ($structure as $structure_key => $array) {
            if (isset($array['key_array'])) {
                // Bỏ qua loại cấu hình theo các key
                continue;
            }
            $output[$structure_key] = array();
            $start_str = $array['start_str'] ? $array['start_str'] : "";
            $start_offset = $array['start_offset'] ? $array['start_offset'] : 0;
            $end_str = $array['end_str'] ? $array['end_str'] : "";
            $end_offset = $array['end_offset'] ? $array['end_offset'] : 0;
            // Theo độ dài, tham số tùy chọn
            $str_length = isset($array['str_length']) ? $array['str_length'] : 0;

            // Lấy string theo vị trí start, end
            if ($start_str && $end_str) {
                $output[$structure_key] = $this->getBetweenTwoStr($data_str, $start_str, $start_offset, $end_str, $end_offset);
            } elseif ($start_str) {
                $output[$structure_key] = $this->getFromStrToEnd($data_str, $start_str, $start_offset);
            } elseif ($end_str) {
                $output[$structure_key] = $this->getFromBeginToStr($data_str, $end_str, $end_offset);
            } else {
                $output[$structure_key] = "";
            }
            // Lấy string theo độ dài
            if ($str_length) {
                $output[$structure_key] = $this->getStrWithLength($output[$structure_key], $str_length);
            }
            // Lấy string theo vị trí bắt đầu
            if (isset($array['start_pos'])) {
                $output[$structure_key] = mb_substr($output[$structure_key], $array['start_pos']);
            }
            // Xử lý chuỗi theo regex
            if (isset($array['regex_match'])) {
                $output[$structure_key] = OperatorUtility::regexMatch($output[$structure_key], $array['regex_match']);
            }
            // Xử lý replace
            if (isset($array['replace_value'])) {
                $output[$structure_key] = OperatorUtility::replaceValue($output[$structure_key], $array['replace_value']);
            }

            // Xử lý convert string sang price
            if (isset($array['is_convert_to_price']) && $array['is_convert_to_price'] == true) {
                $output[$structure_key] = floatval($output[$structure_key]);
            }

            // Xử lý format ngày
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
    public function getBetweenTwoStr($str, $start_str, $start_offset, $end_str, $end_offset)
    {
        $result = "";
        $start_pos = strpos($str, $start_str);
        $end_pos = strpos($str, $end_str);

        if ($start_pos !== false && $end_pos !== false) {
            $start_pos += strlen($start_str);
            $result = substr($str, $start_pos + $start_offset, ($end_pos - $end_offset) - $start_pos);
            $result = trim(str_replace("\n", " ", $result));
        }
        return $result;
    }
    public function getFromStrToEnd($str, $start_str, $start_offset)
    {
        $result = "";
        $start_pos = strpos($str, $start_str);
        if ($start_pos !== false) {
            $start_pos += strlen($start_str);
            $result = substr($str, $start_pos + $start_offset);
            $result = trim(str_replace("\n", " ", $result));
        }
        return $result;
    }
    public function getFromBeginToStr($str, $end_str, $end_offset)
    {
        $result = "";
        $end_pos = strpos($str, $end_str);
        if ($end_pos !== false) {
            $result = substr($str, 0, $end_pos - $end_offset);
            $result = trim(str_replace("\n", " ", $result));
        }
        return $result;
    }
    public function getStrWithLength($str, $str_length)
    {
        $result = "";
        if ($str_length) {
            $result = mb_substr($str, 0, $str_length);
            $result = trim(str_replace("\n", " ", $result));
        } else {
            $result = $str;
        }
        return $result;
    }

}
