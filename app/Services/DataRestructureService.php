<?php

namespace App\Services;

class DataRestructureService implements Interfaces\DataRestructureInterface
{
    public function withArrayMapping($array, $structure)
    {
        $collection = collect([]);
        foreach ($array as $match) {
            $output = [];
            foreach ($structure as $key => $index) {
                $output[$key] = $match[$index];
            }
            $collection->push($output);
        }
        return $collection;
    }
}
