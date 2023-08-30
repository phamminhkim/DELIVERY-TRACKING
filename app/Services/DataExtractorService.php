<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use RandomState\Camelot\Camelot;

class DataExtractorService implements Interfaces\DataExtractorInterface
{
    public function withCamelot($file_path, $flavor)
    {
        if ($flavor == 'stream') {
            $table = Camelot::stream($file_path)->extract();
        } else {
            $table = Camelot::lattice($file_path)->extract();
        }

        return $table;
    }
}
