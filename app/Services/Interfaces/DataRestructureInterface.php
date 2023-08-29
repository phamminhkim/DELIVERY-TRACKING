<?php

namespace App\Services\Interfaces;

interface DataRestructureInterface
{
    public function withRegex($raw_data, $pattern);
    public function withLeagueCsv($raw_data);
}
