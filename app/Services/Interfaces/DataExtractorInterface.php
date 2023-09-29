<?php

namespace App\Services\Interfaces;

interface DataExtractorInterface
{
    public function extract($file_path, $options);
}
