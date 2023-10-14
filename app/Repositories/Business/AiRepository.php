<?php

namespace App\Repositories\Business;

use App\Jobs\HandleUploadFile;
use App\Models\Business\Batch;
use App\Models\Business\ConvertTableConfig;
use App\Models\Business\ExtractDataConfig;
use App\Models\Business\ExtractOrderConfig;
use App\Models\Business\RegexPattern;

use App\Models\Business\RestructureDataConfig;
use App\Models\Business\UploadedFile;
use App\Models\Master\UserMorph;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
        $tables = $this->data_extractor->extract($file_path, $options);
        $exclude_head_tables_count = $this->request->exclude_head_tables_count ?? 0;
        $exclude_tail_tables_count = $this->request->exclude_tail_tables_count ?? 0;
        $choosen_tables = [];
        for ($i = $exclude_head_tables_count; $i < count($tables) - $exclude_tail_tables_count; $i++) {
            $choosen_tables[] = $tables[$i];
        }
        return $choosen_tables;
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

        $table = [];
        for ($i = 0; $i < count($array); $i++) {
            $extracted_table = $this->table_converter->convert($array[$i], $options);
            $table = array_merge($table, $extracted_table);
        }
        return $table;
    }

    private function restructureData($array)
    {
        $options = array();
        if ($this->data_restructure instanceof IndexArrayMappingRestructure) {
            $options['structure'] = json_decode($this->request->structure, true);
        } elseif ($this->data_restructure instanceof KeyArrayMappingRestructure) {
            $options['structure'] = json_decode($this->request->structure, true);
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
            $table_data = json_decode($this->data['table_data'], true);
            return $this->restructureData($table_data);
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }



    public function getExtractOrderConfigs()
    {
        $query = ExtractOrderConfig::query();

        if ($this->request->filled('customer_group_ids')) {
            $customer_groups_ids = $this->request->customer_group_ids;
            $query->whereHas('customer_group', function ($query) use ($customer_groups_ids) {
                $query->whereIn('id', $customer_groups_ids);
            });
        }

        if ($this->request->filled('extract_order_config_ids')) {
            $extract_order_config_ids = $this->request->extract_order_config_ids;
            $query->whereIn('id', $extract_order_config_ids);
        }

        $query->with(['extract_data_config', 'convert_table_config', 'restructure_data_config']);
        $extract_order_configs = $query->get();
        return $extract_order_configs;
    }

    public function createExtractOrderConfigs()
    {
        try {
            $validator = Validator::make($this->data, [
                'customer_group_id' => 'required|exists:customer_groups,id',
                'extract_data_config' => 'required',
                'convert_table_config' => 'required',
                'restructure_data_config' => 'required',
                'name' => 'required|unique:extract_order_configs,name',
            ], [
                'customer_group_id.required' => 'Customer group là bắt buộc',
                'customer_group_id.exists' => 'Customer group không tồn tại',
                'extract_data_config.required' => 'Extract data config là bắt buộc',
                'extract_data_config.json' => 'Extract data config không đúng định dạng json',
                'convert_table_config.required' => 'Convert table config là bắt buộc',
                'convert_table_config.json' => 'Convert table config không đúng định dạng json',
                'restructure_data_config.required' => 'Restructure data config là bắt buộc',
                'restructure_data_config.json' => 'Restructure data config không đúng định dạng json',
                'name.required' => 'Tên là bắt buộc',
                'name.unique' => 'Tên đã tồn tại',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();

                $extract_data_config = ExtractDataConfig::create($this->data['extract_data_config']);

                $this->data['convert_table_config']['manual_patterns'] = json_encode($this->data['convert_table_config']['manual_patterns']);
                $convert_table_config = ConvertTableConfig::create($this->data['convert_table_config']);

                $this->data['restructure_data_config']['structure'] = json_encode($this->data['restructure_data_config']['structure']);
                $restructure_data_config = RestructureDataConfig::create($this->data['restructure_data_config']);

                $extract_order_config = ExtractOrderConfig::create([
                    'name' => $this->data['name'],
                    'customer_group_id' => $this->data['customer_group_id'],
                    'extract_data_config_id' => $extract_data_config->id,
                    'convert_table_config_id' => $convert_table_config->id,
                    'restructure_data_config_id' => $restructure_data_config->id,
                ]);

                DB::commit();
                $extract_order_config->load(['extract_data_config', 'convert_table_config', 'restructure_data_config']);
                return $extract_order_config;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function updateExtractOrderConfig($extract_order_config_id)
    {
        try {
            $validator = Validator::make($this->data, [
                'extract_data_config' => 'required',
                'convert_table_config' => 'required',
                'restructure_data_config' => 'required',
            ], [
                'extract_data_config.required' => 'Extract data config là bắt buộc',
                'convert_table_config.required' => 'Convert table config là bắt buộc',
                'restructure_data_config.required' => 'Restructure data config là bắt buộc',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $extract_order_config = ExtractOrderConfig::query()->with(['extract_data_config', 'convert_table_config', 'restructure_data_config'])->find($extract_order_config_id);
                if (!$extract_order_config) {
                    $this->message = 'Extract order config không tồn tại';
                    return;
                }
                $this->data['convert_table_config']['manual_patterns'] = json_encode($this->data['convert_table_config']['manual_patterns']);
                $this->data['restructure_data_config']['structure'] = json_encode($this->data['restructure_data_config']['structure']);
                $extract_order_config->extract_data_config->update($this->data['extract_data_config']);
                $extract_order_config->convert_table_config->update($this->data['convert_table_config']);
                $extract_order_config->restructure_data_config->update($this->data['restructure_data_config']);

                DB::commit();
                return $extract_order_config;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }


    public function prepareUploadFile()
    {
        try {
            $validator = Validator::make($this->data, [
                'extract_order_config' => 'required|exists:extract_order_configs,id',
            ], [
                'extract_order_config.required' => 'Extract order config id là bắt buộc',
                'extract_order_config.exists' => 'Extract order config id không tồn tại',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $batch = Batch::create(['extract_order_config_id' => $this->data["extract_order_config"]]);
                DB::commit();
                return $batch->id;
            }
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function uploadFile()
    {
        try {
            $validator = Validator::make($this->data, [
                'file' => 'required|file',
                'batch_id' => 'required|uuid|exists:batches,id',
            ], [
                'file.required' => 'File là bắt buộc',
                'file.file' => 'File không đúng định dạng',
                'batch_id.required' => 'Batch id là bắt buộc',
                'batch_id.uuid' => 'Batch id không đúng định dạng uuid',
                'batch_id.exists' => 'Batch id không tồn tại',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $file = $this->request->file('file');
                $file_path = $this->file_service->saveProtectedFile($file, $this->current_user->id, $this->data['batch_id']);
                $uploaded_file = UploadedFile::create([
                    'path' => $file_path,
                    'batch_id' => $this->data['batch_id'],
                ]);
                $user_morph = new UserMorph(['user_id' => $this->current_user->id]);
                $uploaded_file->users()->save($user_morph);
                return $uploaded_file;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function getFile($id)
    {
        $file = UploadedFile::query()->find($id);
        if (!$file) {
            $this->message = 'File không tồn tại';
            return;
        }
        // dd($file->users->pluck('morphable_id')->toArray(), $this->current_user->id);
        if (!(in_array($this->current_user->id, $file->users->pluck('user_id')->toArray()) || $this->current_user->hasRole('admin'))) {
            $this->message = 'Bạn không có quyền truy cập file này';
            return;
        }
        $file_path = $file->path;
        return Storage::disk('protected')->download($file_path);
    }
}
