<?php

namespace App\Services\Excel;

use App\Services\Implementations\Files\LocalFileService;

class ExcelExtractor
{
    public function extractData($file)
    {
        $local_file_service = new LocalFileService();
        $file_path = null;
        $raw_table_data = [];

        try {
            $file_path = $local_file_service->saveTemporaryFile($file);
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file_path);
            $worksheet = $spreadsheet->getActiveSheet();

            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // This loops through all cells
                $data = [];
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }
                $raw_table_data[] = $data;
            }

            // Remove the first row (header)
            $raw_table_data = array_slice($raw_table_data, 1);
        } catch (\Exception $exception) {
            // Handle exception if needed
        } finally {
            if (isset($file_path)) {
                $local_file_service->deleteTemporaryFile($file_path);
            }
        }

        return $raw_table_data;
    }

    public function structureData($raw_table_data, $template_structure, $main_key = null)
    {
        $table_data = [];
        foreach ($raw_table_data as $row_data) {
            $structured_row_data = [];
            foreach ($template_structure as $key => $row_data_index) {
                $structured_row_data[$key] = $row_data[$row_data_index];
            }
            if ($main_key) {
                if (
                    !isset($table_data[$structured_row_data[$main_key]]) &&
                    $structured_row_data[$main_key] != null &&
                    strlen($structured_row_data[$main_key]) != 0
                ) {
                    $table_data[$structured_row_data[$main_key]] = [];
                }
                if (!isset($structured_row_data[$main_key]) || $structured_row_data[$main_key] == null || strlen($structured_row_data[$main_key]) == 0) {
                    continue;
                }
                $table_data[$structured_row_data[$main_key]][] = $structured_row_data;
            } else {
                $table_data[] = $structured_row_data;
            }
        }
        return $table_data;
    }
}
