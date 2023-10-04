<?php

namespace App\Repositories\Business;

use App\Models\Business\ExtractOrderConfig;
use App\Models\Business\RegexPattern;

use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Implementations\Converters\LeagueCsvConverter;
use App\Services\Implementations\Converters\ManualConverter;
use App\Services\Implementations\Converters\RegexMatchConverter;
use App\Services\Implementations\Converters\RegexSplitConverter;
use App\Services\Implementations\Extractors\CamelotExtractorService;
use App\Services\Implementations\Restructurers\IndexArrayMappingRestructure;
use App\Services\Implementations\Restructurers\KeyArrayMappingRestructure;
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
        $options = array();
        if ($this->data_extractor instanceof CamelotExtractorService) {
            $options['is_merge_pages'] = $this->request->is_merge_pages ?? false;
            $options['flavor'] = $this->request->camelot_flavor ?? 'lattice'; // Lưu trữ 'stream' hoặc 'lattice' với từng trường hợp
        }
        $table = $this->data_extractor->extract($file_path, $options);
        return $table;
    }

    private function convertToTable($array)
    {
        $options = array();
        if ($this->table_converter instanceof RegexMatchConverter) {
            $options['regex_pattern'] = $this->request->regex_pattern;
        } elseif ($this->table_converter instanceof RegexSplitConverter) {
            $options['regex_pattern'] = $this->request->regex_pattern;
        } elseif ($this->table_converter instanceof LeagueCsvConverter) {
        } elseif ($this->table_converter instanceof ManualConverter) {
            $manual_patterns = json_decode($this->request->manual_patterns);
            $options['manual_patterns'] = $manual_patterns;
        }
        $exclude_head_tables_count = $this->request->exclude_head_tables_count ?? 0;
        $exclude_tail_tables_count = $this->request->exclude_tail_tables_count ?? 0;
        $table = [];
        for ($i = $exclude_head_tables_count; $i < count($array) - $exclude_tail_tables_count; $i++) {
            $extracted_table = $this->table_converter->convert($array[$i], $options);
            $table = array_merge($table, $extracted_table);
        }
        return $table;
    }

    private function restructureData($array)
    {
        $options = array();
        if ($this->data_restructure instanceof IndexArrayMappingRestructure) {
            $options['structure'] = json_decode($this->request->structure);
        } elseif ($this->data_restructure instanceof KeyArrayMappingRestructure) {
            $options['structure'] = json_decode($this->request->structure);
        }
        $table = $this->data_restructure->restructure($array, $options);
        return $table;
    }

    public function extractDataForConfig()
    {
        try {
            $file = $this->request->file('file');
            $file_path = $this->file_service->saveTemporaryFile($file);
            $raw_data = $this->extractData($file_path);
            $this->file_service->deleteTemporaryFile($file_path);
            return $raw_data;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function convertToTableForConfig()
    {
        try {
            $raw_table_data = json_decode($this->data['raw_table_data']);
            return $this->convertToTable($raw_table_data);
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function restructureDataForConfig()
    {
        try {
            $table_data = json_decode($this->data['table_data']);
            return $this->restructureData($table_data);
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function getExtractOrderConfigs($customer_groups_id)
    {
        $query = ExtractOrderConfig::query()
            ->whereHas('customer_group', function ($query) use ($customer_groups_id) {
                $query->where('id', $customer_groups_id);
            });
        $extract_order_config = $query->first();
        return array(
            "extract_data_config" => $extract_order_config->extract_data_config,
            "convert_to_table_config" => $extract_order_config->convert_table_config,
            "restructure_data_config" => $extract_order_config->restructure_data_config,
        );
    }
}
