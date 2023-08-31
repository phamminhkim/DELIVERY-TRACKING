<?php

namespace App\Repositories\Business;

use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Interfaces\DataExtractorInterface;
use App\Services\Interfaces\DataRestructureInterface;
use App\Services\Interfaces\FileServiceInterface;
use App\Services\Interfaces\PdfExtractorInterface;
use Illuminate\Support\Facades\Storage;
use RandomState\Camelot\Camelot;
use League\Csv\Reader;

class AiRepository extends RepositoryAbs
{
    protected $file_service;
    protected $data_extractor;
    protected $data_restructure;

    public function __construct(
        FileServiceInterface $file_service,
        DataExtractorInterface $data_extractor,
        DataRestructureInterface $data_restructure,
        $request
    ) {
        parent::__construct($request);
        $this->file_service = $file_service;
        $this->data_extractor = $data_extractor;
        $this->data_restructure = $data_restructure;
    }
    public function extractOrder()
    {
        try {
            $file = $this->request->file('file');
            $file_path = $this->file_service->saveTemporaryFile($file);
            $raw_data = $this->extractData($file_path);
            $table_data = $this->convertToTable($raw_data);
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
            }
            if ($table) return $table;
        }
        throw new \Exception('Extract method is not specified');
    }

    private function convertToTable($array)
    {
        if ($this->request->filled('convert_method')) {
            $method = $this->request->convert_method; // Có thể là regex, leaguecsv
            $table = null;
            if ($method == 'regexmatch') {
                $pattern = $this->request->regex_pattern;
                $table = $this->data_restructure->withRegexMatch($array[0], $pattern);
            } elseif ($method == 'regexsplit') {
                $pattern = $this->request->regex_pattern;
                $table = $this->data_restructure->withRegexSplit($array[0], $pattern);
            } elseif ($method == 'leaguecsv') {
                $table = $this->data_restructure->withLeagueCsv($array);
            }
            if ($table) return $table;
        }
        throw new \Exception('Convert table method is not specified');
    }

    private function restructureData($array)
    {
        dd($array);
        $structure = [
            'Barcode' => 1,
            'Unit' => 2,
            'Number' => 3,
            'ProductID' => 4,
            'Quantity' => 5,
            'UnitPrice' => 6,
            'Amount' => 7,
            'Description' => 8,
        ];
        $collection = collect([]);
        foreach ($array as $match) {
            $output = [];
            foreach ($structure as $key => $index) {
                $output[$key] = $match[$index];
            }
            $collection->push($output);
        }
        return $collection;
    }
}
