<?php

namespace App\Services\Interfaces;

interface TableConverterInterface
{
    public function withRegexMatch($raw_data, $pattern);
    public function withRegexSplit($raw_data, $pattern);
    public function withLeagueCsv($raw_data);
    public function withManualPattern($raw_data, $manual_patterns);
}
