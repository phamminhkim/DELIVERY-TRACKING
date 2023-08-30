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
            if ($method == 'camelot') {
                $flavor = $this->request->camelot_flavor ?? 'lattice'; // Lưu trữ 'stream' hoặc 'lattice' với từng trường hợp
                $table = $this->data_extractor->withCamelot($file_path, $flavor);
                return $table;
            }
            return [];
        } else {
            throw new \Exception('Extract method is not specified');
        }
    }

    private function convertToTable($array)
    {
        $data = $array[0];

        // Lưu regex pattern và structure tương ứng với từng trường hợp
        if ($this->request->filled('regex_pattern')) {
            $pattern = $this->request->regex_pattern;
            $collection = $this->data_restructure->withRegex($data, $pattern);

            return $collection;
        } else {
            $collection = $this->data_restructure->withLeagueCsv($data);
            return $collection;
        }
    }

    private function restructureData($array)
    {
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
