<?php

namespace App\Services\Interfaces;

interface TableConverterInterface
{
    public function convert($raw_data, $options);
}
