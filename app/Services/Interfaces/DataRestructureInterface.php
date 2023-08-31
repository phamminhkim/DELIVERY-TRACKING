<?php

namespace App\Services\Interfaces;

interface DataRestructureInterface
{
    public function withArrayMappingByIndex($array, $structure);
    public function withArrayMappingByKey($array, $structure);
}
