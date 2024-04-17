<?php

namespace App\Services\Implementations\Extractors;

use App\Services\Interfaces\DataExtractorInterface;

class ExcelExtractorService implements DataExtractorInterface
{
    public function extract($file_path, $options)
    {
        $table_area_info = $options['table_area_info'];
        $start_row = $table_area_info->start_row;
        $start_row = $start_row ? $start_row : 1;
        $start_column = $table_area_info->start_column;
        $start_column = $start_column ? $start_column : 1;
        $end_row = $table_area_info->end_row;
        $end_column = $table_area_info->end_column;

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file_path);
        $worksheet = $spreadsheet->getActiveSheet();
        $raw_table_data = [];
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }
            $raw_table_data[] = $data;
        }
        $limit_row = $end_row ? ($end_row - $start_row + 1) : null;
        $limit_column = $end_column ? ($end_column - $start_column + 1) : null;
        // Theo start row và end row
        $raw_table_data = array_slice($raw_table_data, $start_row - 1, $limit_row);
        // Theo start column và end column
        $raw_table_data = array_map(function ($row) use ($start_column, $limit_column) {
            return array_slice($row, $start_column - 1, $limit_column);
        }, $raw_table_data);

        return $raw_table_data;
    }
}
