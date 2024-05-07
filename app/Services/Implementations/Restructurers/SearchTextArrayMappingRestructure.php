<?php

namespace App\Services\Implementations\Restructurers;

use App\Services\Interfaces\DataRestructureInterface;
use App\Utilities\FormatDateUtility;

class SearchTextArrayMappingRestructure implements DataRestructureInterface
{
    public function restructure($data, $options)
    {
        try {
            $structure = $options['structure'];
            $data_str = "";
            $output = array();
            foreach ($data as $data_arr) {
                $data_str .= implode(' ', $data_arr);
            }
            foreach ($structure as $structure_key => $array) {
                $output[$structure_key] = array();
                $start_str = $array['start_str'] ? $array['start_str'] : "";
                $start_offset = $array['start_offset'] ? $array['start_offset'] : 0;
                $end_str = $array['end_str'] ? $array['end_str'] : "";
                $end_offset = $array['end_offset'] ? $array['end_offset'] : 0;

                if ($start_str && $end_str) {
                    $output[$structure_key] = $this->getBetweenTwoStr($data_str, $start_str, $start_offset, $end_str, $end_offset);
                } elseif ($start_str) {
                    $output[$structure_key] = $this->getFromStrToEnd($data_str, $start_str, $start_offset);
                } elseif ($end_str) {
                    $output[$structure_key] = $this->getFromBeginToStr($data_str, $end_str, $end_offset);
                } else {
                    $output[$structure_key] = "";
                }

                if ($output[$structure_key] && isset($array['date_format'])) {
                    $output[$structure_key] = FormatDateUtility::formatDate2Date($array['date_format'], 'Y-m-d', $output[$structure_key]);
                }

            }
            return $output;
        } catch (\Throwable $th) {
            dd($th);
            return null;
        }
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

}
