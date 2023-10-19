<?php

namespace App\Repositories\Business;

use App\Enums\Ai\Convert\ExtractErrors;
use App\Jobs\HandleUploadFile;
use App\Models\Business\Batch;
use App\Models\Business\ConvertTableConfig;
use App\Models\Business\ExtractDataConfig;
use App\Models\Business\ExtractError;
use App\Models\Business\ExtractOrderConfig;
use App\Models\Business\FileExtractErrorLog;
use App\Models\Business\RawExtractHeader;
use App\Models\Business\RawExtractItem;
use App\Models\Business\RawExtractItems;
use App\Models\Business\RegexPattern;

use App\Models\Business\RestructureDataConfig;
use App\Models\Business\UploadedFile;
use App\Models\Master\CustomerMaterial;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RandomState\Camelot\Camelot;
use League\Csv\Reader;
use App\Enums\Ai\Error\ExtractErrors as ExtractErrorsEnum;
use App\Models\Business\FileExtractError;
use App\Models\Business\RawSoHeader;
use App\Models\Business\RawSoItem;
use App\Models\Master\SapMaterial;

class AiRepository extends RepositoryAbs
{
    protected $file_service;
    protected $data_extractor;
    protected $table_converter;
    protected $data_restructure;

    protected $data_extractor_instances;
    protected $table_converter_instances;
    protected $data_restructure_instances;

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
        $this->data_extractor_instances = [
            'camelot' => new CamelotExtractorService(),
        ];
        $this->table_converter_instances = [
            'regexmatch' => new RegexMatchConverter(),
            'regexsplit' => new RegexSplitConverter(),
            'leaguecsv' => new LeagueCsvConverter(),
            'manual' => new ManualConverter(),
        ];
        $this->data_restructure_instances = [
            'arraymappingbyindex' => new IndexArrayMappingRestructure(),
            'arraymappingbykey' => new KeyArrayMappingRestructure(),
        ];
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

    public function extractOrderFromUploadedFile($file_id)
    {
        try {
            $file_record = UploadedFile::query()->with(['batch', 'batch.customer.group'])->find($file_id);
            if (!$file_record) {
                $this->message = 'File không tồn tại';
                return;
            }
            $file_path = Storage::disk('protected')->path($file_record->path);
            $extract_order_config = ExtractOrderConfig::query()->with(['extract_data_config', 'convert_table_config', 'restructure_data_config'])->find($file_record->batch->extract_order_config_id);

            $this->data_extractor = $this->data_extractor_instances[$extract_order_config->extract_data_config->method];
            try {
                $raw_data = $this->extractData($file_path, $extract_order_config->extract_data_config);
            } catch (\Throwable $exception) {

                $extract_error = ExtractError::query()->where('code', ExtractErrorsEnum::EXTRACT_ERROR)->first();
                FileExtractError::create([
                    'uploaded_file_id' => $file_record->id,
                    'extract_error_id' => $extract_error->id,
                ]);
                throw $exception;
            }

            $this->table_converter = $this->table_converter_instances[$extract_order_config->convert_table_config->method];
            try {
                $table_data = $this->convertToTable($raw_data, $extract_order_config->convert_table_config);
            } catch (\Throwable $exception) {

                $convert_error = ExtractError::query()->where('code', ExtractErrorsEnum::CONVERT_ERROR)->first();
                FileExtractError::create([
                    'uploaded_file_id' => $file_record->id,
                    'extract_error_id' => $convert_error->id,
                ]);
                throw $exception;
            }


            $this->data_restructure = $this->data_restructure_instances[$extract_order_config->restructure_data_config->method];
            try {
                $final_data = $this->restructureData($table_data, $extract_order_config->restructure_data_config);
            } catch (\Throwable $exception) {

                $restructure_error = ExtractError::query()->where('code', ExtractErrorsEnum::RESTRUCTURE_ERROR)->first();
                FileExtractError::create([
                    'uploaded_file_id' => $file_record->id,
                    'extract_error_id' => $restructure_error->id,
                ]);
                throw $exception;
            }

            $customer_group = $file_record->batch->customer->group;

            // $created_extract_items = collect([]);
            $created_extract_items = new  \Illuminate\Database\Eloquent\Collection([]);
            $error_extract_items = [];
            DB::beginTransaction();
            $raw_extract_header = RawExtractHeader::firstOrCreate([
                'customer_id' => $file_record->batch->customer_id,
                'uploaded_file_id' => $file_record->id,

            ]);
            foreach ($final_data as $item) {
                if (!isset($item['ProductID']) || $item['ProductID'] == '') {
                    continue;
                }
                $customer_material = CustomerMaterial::query()
                    ->where('customer_group_id', $customer_group->id)
                    ->where('customer_sku_code', $item['ProductID'])
                    ->where('customer_sku_name', $item['ProductName'])
                    ->first();
                if (!$customer_material) {
                    $error_extract_items[] = 'Không tìm thấy customer material với ProductID: ' . $item['ProductID'] . ' và ProductName: ' . $item['ProductName'] . ' của customer group: ' . $customer_group->name;
                    continue;
                }
                $raw_extract_item = RawExtractItem::firstOrCreate([
                    'raw_extract_header_id' => $raw_extract_header->id,
                    'customer_material_id' => $customer_material->id,
                    'quantity' => $item['Quantity'],
                ]);
                $created_extract_items->push($raw_extract_item);
            }
            DB::commit();
            if (count($error_extract_items) > 0) {
                $not_found_customer_error = ExtractError::query()->where('code', ExtractErrorsEnum::NOT_FOUND_CUSTOMER_MATERIAL)->first();
                $error_log = json_encode($error_extract_items);
                $file_extract_error = FileExtractError::create([
                    'uploaded_file_id' => $file_record->id,
                    'extract_error_id' => $not_found_customer_error->id,
                ]);
                FileExtractErrorLog::create([
                    'file_extract_error_id' => $file_extract_error->id,
                    'log' => $error_log,
                ]);
            }
            // return array(
            //     'created_items' => $created_items,
            //     'error_items' => $error_items,
            // );

            $created_so_items = collect([]);
            $error_so_items = [];
            DB::beginTransaction();
            $raw_so_header = RawSoHeader::firstOrCreate(
                array_merge(
                    $raw_extract_header->toArray(),
                    [
                        'raw_extract_header_id' => $raw_extract_header->id,
                        'serial_number' => strtoupper(uniqid($file_record->batch->customer->code)),
                    ]
                )
            );
            $created_extract_items->load(['customer_material.mappings.sap_material']);

            foreach ($created_extract_items as $item) {
                $sap_material_mappings = $item->customer_material->mappings;
                if (count($sap_material_mappings) == 0) {
                    $error_so_items[] = 'Không tìm thấy sap material với customer material id: ' . $item->customer_material->id;
                    continue;
                }
                foreach ($sap_material_mappings as $mapping) {
                    $sap_material = $mapping->sap_material;
                    $quantity = round(($item->quantity) * ($mapping->percentage / 100), 0);
                    $raw_so_item = RawSoItem::firstOrCreate([
                        'raw_extract_item_id' => $item->id,
                        'raw_so_header_id' => $raw_so_header->id,
                        'sap_material_id' => $sap_material->id,
                        'quantity' => $quantity,
                        'percentage' => $mapping->percentage,
                    ]);
                    $created_so_items->push($raw_so_item);
                }
            }
            DB::commit();
            if (count($error_so_items) > 0) {
                $not_found_sap_error = ExtractError::query()->where('code', ExtractErrorsEnum::NOT_FOUND_SAP_MATERIAL)->first();
                $error_log = json_encode($error_so_items);
                $file_extract_error = FileExtractError::create([
                    'uploaded_file_id' => $file_record->id,
                    'extract_error_id' => $not_found_sap_error->id,
                ]);
                FileExtractErrorLog::create([
                    'file_extract_error_id' => $file_extract_error->id,
                    'log' => $error_log,
                ]);
            }

            return array(
                'created_extract_items' => $created_extract_items,
                'error_extract_items' => $error_extract_items,
                'created_so_items' => $created_so_items,
                'error_so_items' => $error_so_items,
            );
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    private function extractData($file_path, $extract_data_config = null)
    {
        $options = array();
        if ($this->data_extractor instanceof CamelotExtractorService) {
            if (!$extract_data_config) {
                $options['is_merge_pages'] = $this->request->is_merge_pages ?? false;
                $options['flavor'] = $this->request->camelot_flavor ?? 'lattice'; // Lưu trữ 'stream' hoặc 'lattice' với từng trường hợp
            } else {
                $options['is_merge_pages'] = $extract_data_config->is_merge_pages ?? false;
                $options['flavor'] = $extract_data_config->camelot_flavor ?? 'lattice'; // Lưu trữ 'stream' hoặc 'lattice' với từng trường hợp
            }
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

    private function convertToTable($array, $convert_table_config = null)
    {
        $options = array();
        if ($this->table_converter instanceof RegexMatchConverter) {
            if (!$convert_table_config) {
                $options['regex_pattern'] = $this->request->regex_pattern;
            } else {
                $options['regex_pattern'] = $convert_table_config->regex_pattern;
            }
        } elseif ($this->table_converter instanceof RegexSplitConverter) {
            if (!$convert_table_config) {
                $options['regex_pattern'] = $this->request->regex_pattern;
            } else {
                $options['regex_pattern'] = $convert_table_config->regex_pattern;
            }
        } elseif ($this->table_converter instanceof LeagueCsvConverter) {
        } elseif ($this->table_converter instanceof ManualConverter) {
            if (!$convert_table_config) {
                $manual_patterns = json_decode($this->request->manual_patterns);
                $options['manual_patterns'] = $manual_patterns;
            } else {
                $manual_patterns = json_decode($convert_table_config->manual_patterns);
                $options['manual_patterns'] = $manual_patterns;
            }
        }

        $table = [];
        for ($i = 0; $i < count($array); $i++) {
            $extracted_table = $this->table_converter->convert($array[$i], $options);
            $table = array_merge($table, $extracted_table);
        }
        return $table;
    }

    private function restructureData($array, $restructure_data_config = null)
    {
        $options = array();
        if ($this->data_restructure instanceof IndexArrayMappingRestructure) {
            if (!$restructure_data_config) {
                $options['structure'] = json_decode($this->request->structure, true);
            } else {
                $options['structure'] = json_decode($restructure_data_config->structure, true);
            }
        } elseif ($this->data_restructure instanceof KeyArrayMappingRestructure) {
            if (!$restructure_data_config) {
                $options['structure'] = json_decode($this->request->structure, true);
            } else {
                $options['structure'] = json_decode($restructure_data_config->structure, true);
            }
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

        $query
            ->with(['extract_data_config', 'convert_table_config', 'restructure_data_config']);
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
                'customer' => 'required|exists:customers,id',
                'company' => 'required|exists:companies,code',
            ], [
                'extract_order_config.required' => 'Extract order config id là bắt buộc',
                'extract_order_config.exists' => 'Extract order config id không tồn tại',
                'customer.required' => 'Customer id là bắt buộc',
                'customer.exists' => 'Customer id không tồn tại',
                'company.required' => 'Company code là bắt buộc',
                'company.exists' => 'Company code không tồn tại',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $original_extract_order_config = ExtractOrderConfig::query()->with(['extract_data_config', 'convert_table_config', 'restructure_data_config'])->find($this->data["extract_order_config"]);
                if (!$original_extract_order_config) {
                    $this->message = 'Extract order config không tồn tại';
                    return;
                }
                $extract_order_config = $original_extract_order_config->replicate()->fill(['name' => 'waiting_to_add']);

                $extract_data_config = $original_extract_order_config->extract_data_config->replicate();
                $convert_table_config = $original_extract_order_config->convert_table_config->replicate();
                $restructure_data_config = $original_extract_order_config->restructure_data_config->replicate();
                $extract_data_config->save();
                $convert_table_config->save();
                $restructure_data_config->save();
                $extract_order_config->save();
                $batch = Batch::create([
                    'extract_order_config_id' => $extract_order_config->id,
                    'customer_id' => $this->data['customer'],
                    'company_code' => $this->data['company'],
                ]);

                $extract_order_config->fill([
                    'name' => $original_extract_order_config->name . ' - ' . $batch->id,
                    'extract_data_config_id' => $extract_data_config->id,
                    'convert_table_config_id' => $convert_table_config->id,
                    'restructure_data_config_id' => $restructure_data_config->id,
                    'reference_id' => $original_extract_order_config->id,
                    'is_official' => false
                ])->save();

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
                DB::beginTransaction();
                $file = $this->request->file('file');
                $file_path = $this->file_service->saveProtectedFile($file, $this->current_user->id, $this->data['batch_id']);
                $uploaded_file = UploadedFile::create([
                    'path' => $file_path,
                    'batch_id' => $this->data['batch_id'],
                ]);
                $user_morph = new UserMorph(['user_id' => $this->current_user->id]);
                $uploaded_file->users()->save($user_morph);
                DB::commit();
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
