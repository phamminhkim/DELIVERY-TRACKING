<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use RandomState\Camelot\Areas;
use RandomState\Camelot\Camelot;

class DataExtractorService implements Interfaces\DataExtractorInterface
{
    public function withCamelot($file_path, $flavor)
    {
        $file_path = "\"" . $file_path . "\"";

        $instance = new Camelot($file_path, $flavor);
        //$instance->inRegions(
        //    Areas::from($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight)
        //);
        $table = $instance->extract();
        return $table;
    }
}
