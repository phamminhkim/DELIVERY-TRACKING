<?php

namespace App\Services\Interfaces;

interface DataExtractorInterface
{
    public function withCamelot($file_path, $flavor);
}
