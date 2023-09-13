<?php

namespace App\Repositories\Business;


use setasign\Fpdi\Fpdi;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Interfaces\DataExtractorInterface;
use App\Services\Interfaces\DataRestructureInterface;
use App\Services\Interfaces\FileServiceInterface;
use App\Services\Interfaces\PdfExtractorInterface;
use App\Services\Interfaces\TableConverterInterface;
use Illuminate\Support\Facades\Storage;
use RandomState\Camelot\Camelot;
use League\Csv\Reader;

class AiRepository extends RepositoryAbs
{
    protected $file_service;
    protected $data_extractor;
    protected $table_converter;
    protected $data_restructure;

    public function __construct(
        FileServiceInterface $file_service,
        DataExtractorInterface $data_extractor,
        TableConverterInterface $table_converter,
        DataRestructureInterface $data_restructure,
        $request
    ) {
        parent::__construct($request);
        $this->file_service = $file_service;
        $this->data_extractor = $data_extractor;
        $this->table_converter = $table_converter;
        $this->data_restructure = $data_restructure;
    }
    public function extractOrder()
    {
        try {
            $file = $this->request->file('file');
            $file_path = $this->file_service->saveTemporaryFile($file);

            if ($this->request->filled('is_merge_pages') && $this->request->is_merge_pages) {
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

            $raw_data = $this->extractData($file_path);

            // preg_match_all("/SKU Number[^\n]*\n(.*?Sub Total)/s", $raw_data[0], $matches, PREG_SET_ORDER);
            // $raw_data = $matches[0];
            // $pattern = '/^"(\d+-\d+)\s+(.*?)\s+(\d+)\s+(.*?)\s+(\w+)\s+(\w+)\s+(\d+\.\d+)\s+(\d+\.\d+)\s+(\d+\.\d+)\s+(\d+\.\d+)\s+(\d+\.\d+)\s+(\d+,\d+\.\d+)"$/m';
            // preg_match_all($pattern, $raw_data[0], $matches, PREG_SET_ORDER);
            // dd($matches);
            $table_data = $this->convertToTable($raw_data);
            // dd($table_data);
            $final_data = $this->restructureData($table_data);
            $this->file_service->deleteTemporaryFile($file_path);
            return $final_data;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    private function extractData($file_path)
    {
        if ($this->request->filled('extract_method')) {
            $method = $this->request->extract_method; // Có thể là camelot, googleai, ocr
            $table = null;
            if ($method == 'camelot') {
                $flavor = $this->request->camelot_flavor ?? 'lattice'; // Lưu trữ 'stream' hoặc 'lattice' với từng trường hợp
                $table = $this->data_extractor->withCamelot($file_path, $flavor);
            } else {
                throw new \Exception('Extract method is not specified');
            }
            return $table;
        } else {
            throw new \Exception('Extract method is not specified');
        }
    }

    private function convertToTable($array)
    {
        if ($this->request->filled('convert_method')) {
            $method = $this->request->convert_method; // Có thể là regex, leaguecsv
            $exclude_head_tables_count = $this->request->exclude_head_tables_count ?? 0;
            $exclude_tail_tables_count = $this->request->exclude_tail_tables_count ?? 0;
            $table = [];
            if ($method == 'regexmatch') {
                $pattern = $this->request->regex_pattern;
                for ($i = $exclude_head_tables_count; $i < count($array) - $exclude_tail_tables_count; $i++) {
                    $extracted_table = $this->table_converter->withRegexMatch($array[$i], $pattern);
                    $table = array_merge($table, $extracted_table);
                }
            } elseif ($method == 'regexsplit') {
                $pattern = $this->request->regex_pattern;
                for ($i = $exclude_head_tables_count; $i < count($array) - $exclude_tail_tables_count; $i++) {
                    $extracted_table = $this->table_converter->withRegexSplit($array[$i], $pattern);
                    $table = array_merge($table, $extracted_table);
                }
            } elseif ($method == 'leaguecsv') {
                for ($i = $exclude_head_tables_count; $i < count($array) - $exclude_tail_tables_count; $i++) {
                    $extracted_table = $this->table_converter->withLeagueCsv($array[$i]);
                    $table = array_merge($table, $extracted_table->toArray());
                }
            } else {
                throw new \Exception('Convert method is not specified');
            }
            return $table;
        } else {
            throw new \Exception('Convert method is not specified');
        }
    }

    private function restructureData($array)
    {
        if ($this->request->filled('restructure_method')) {
            $method = $this->request->restructure_method;;
            $table = null;
            $structure = json_decode($this->request->structure);
            if ($method == 'arraymappingbyindex') {
                $table = $this->data_restructure->withArrayMappingByIndex($array, $structure);
            } elseif ($method == 'arraymappingbykey') {
                $table = $this->data_restructure->withArrayMappingByKey($array, $structure);
            }
            return $table;
        }
        return $array;
    }
}
