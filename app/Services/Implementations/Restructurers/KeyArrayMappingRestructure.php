<?php

namespace App\Services\Implementations\Restructurers;

use App\Services\Interfaces\DataRestructureInterface;

class KeyArrayMappingRestructure implements DataRestructureInterface
{
    public function restructure($data, $options)
    {

        $structure = $options['structure'];
        $collection = collect([]);
        foreach ($data as $match) {
            $output = [];
            foreach ($structure as $key => $index) {
                $output[$key] = $match[$index];
            }
            $collection->push($output);
        }
        return $collection->toArray();
    }
}
