<?php

namespace App\Services\Implementations\Restructurers;

use App\Services\Interfaces\DataRestructureInterface;
use App\Utilities\FormatDateUtility;

class MergeIndexArrayMappingRestructure implements DataRestructureInterface
{
    public function restructure($data, $options)
    {
        try {
            $structure = $options['structure'];
            $output = array();
            foreach ($structure as $structure_key => $array) {
                $output[$structure_key] = array();
                if (isset($array['index'])) {
                    foreach ($array['index'] as $array_key => $array_value) {
                        $child_value = $data[$array_value['parent_index']][$array_value['child_index']];
                        if ($child_value) {
                            array_push($output[$structure_key], $child_value);
                        }
                    }
                }
                $output[$structure_key] = $output[$structure_key] ? implode(" ", $output[$structure_key]) : "";
                if (isset($array['date_format']) && $output[$structure_key]) {
                    $output[$structure_key] = FormatDateUtility::formatDate2Date($array['date_format'], 'Y-m-d', $output[$structure_key]);
                }
            }
            return $output;
        } catch (\Throwable $th) {
            dd($th);
            return null;
        }
    }
}
