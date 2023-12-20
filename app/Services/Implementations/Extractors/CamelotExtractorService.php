<?php

namespace App\Services\Implementations\Extractors;

use App\Services\Interfaces\DataExtractorInterface;
use RandomState\Camelot\Areas;
use RandomState\Camelot\Camelot;
use setasign\Fpdi\Fpdi;

class CamelotExtractorService implements DataExtractorInterface
{
    public function extract($file_path, $options)
    {
        $file_path = "\"" . $file_path . "\"";
        $instance = new Camelot($file_path, $options['flavor']);

        // Nếu để mặc định thì Camelot chỉ lấy 1 page đầu
        if ($options['is_merge_pages']) {
            $instance->pages('1-end');
        }

        $table = $instance->extract();
        return $table;
    }
}
