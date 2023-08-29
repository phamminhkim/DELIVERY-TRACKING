<?php

namespace App\Services\Interfaces;

interface DataExtractorInterface
{
    public function extractData($file_path, $flavor);
}
