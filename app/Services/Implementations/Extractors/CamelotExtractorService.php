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
        if ($options['is_merge_pages']) {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($file_path);
            $size = $pdf->getTemplateSize($pdf->importPage(1));
            $pdf->AddPage('P', [$size['width'], $size['height'] * $pageCount]);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl = $pdf->importPage($i);
                $pdf->useTemplate($tpl, 0, $size['height'] * ($i - 1));
            }
            $pdf->Output('F', $file_path);
        }

        $file_path = "\"" . $file_path . "\"";

        $instance = new Camelot($file_path, $options['flavor']);
        //$instance->inRegions(
        //    Areas::from($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight)
        //);
        $table = $instance->extract();
        return $table;
    }
}
