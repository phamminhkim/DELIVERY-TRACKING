<?php

namespace App\Services\Interfaces;

interface DataRestructureInterface
{
    public function withRegexMatch($raw_data, $pattern);
    public function withRegexSplit($raw_data, $pattern);
    public function withLeagueCsv($raw_data);
}
