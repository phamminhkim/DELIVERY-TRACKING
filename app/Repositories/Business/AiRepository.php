<?php

namespace App\Repositories\Business;

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
            $table = null;
            if ($method == 'regexmatch') {
                $pattern = $this->request->regex_pattern;
                $table = $this->table_converter->withRegexMatch($array[0], $pattern);
            } elseif ($method == 'regexsplit') {
                $pattern = $this->request->regex_pattern;
                $table = $this->table_converter->withRegexSplit($array[0], $pattern);
            } elseif ($method == 'leaguecsv') {
                $table = $this->table_converter->withLeagueCsv($array[0]);
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
        if ($this->request->filled('restucture_method')) {
            $method = $this->request->restucture_method;;
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
