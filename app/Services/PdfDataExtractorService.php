<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use RandomState\Camelot\Camelot;

class PdfDataExtractorService implements Interfaces\DataExtractorInterface
{
    public function extractData($file_path)
    {
        $table = Camelot::lattice($file_path)->extract();

        return $table;
    }
}
