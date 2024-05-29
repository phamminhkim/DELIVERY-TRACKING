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
use App\Models\Master\CustomerPromotion;
use App\Models\Master\CustomerPartner;
use App\Models\Master\UserMorph;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Implementations\Converters\LeagueCsvConverter;
use App\Services\Implementations\Converters\ManualConverter;
use App\Services\Implementations\Converters\RegexMatchConverter;
use App\Services\Implementations\Converters\RegexSplitConverter;
use App\Services\Implementations\Extractors\CamelotExtractorService;
use App\Services\Implementations\Extractors\ExcelExtractorService;
use App\Services\Implementations\Restructurers\IndexArrayMappingRestructure;
use App\Services\Implementations\Restructurers\KeyArrayMappingRestructure;
use App\Services\Implementations\Restructurers\MergeIndexArrayMappingRestructure;
use App\Services\Implementations\Restructurers\SearchTextArrayMappingRestructure;
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
use App\Enums\File\FileStatuses;
use App\Exceptions\Ai\NotFoundCustomerMaterialException;
use App\Exceptions\Ai\NotFoundSapMaterialException;
use App\Jobs\ExtractFile;
use App\Models\Business\FileExtractError;
use App\Models\Business\FileStatus;
use App\Models\Business\RawSoHeader;
use App\Models\Business\RawSoItem;
use App\Models\Master\Warehouse;
use App\Utilities\UniqueIdUtility;
use Exception;
use Illuminate\Http\Request;

class AiRepository extends RepositoryAbs
{
    protected $file_service;
    protected $data_extractor;
    protected $table_converter;
    protected $data_restructure;

    protected $header_extractor;
    protected $header_table_converter;
    protected $header_restructure;

    protected $data_extractor_instances;
    protected $table_converter_instances;
    protected $data_restructure_instances;

    protected $header_extractor_instances;
    protected $header_table_converter_instances;
    protected $header_restructure_instances;

    protected $customer_group_id;

    public function __construct(
        FileServiceInterface $file_service,
        DataExtractorInterface $data_extractor,
        TableConverterInterface $table_converter,
        DataRestructureInterface $data_restructure,
        DataExtractorInterface $header_extractor,
        TableConverterInterface $header_table_converter,
        DataRestructureInterface $header_restructure,
        $customer_group_id,
        $request
    ) {
        parent::__construct($request);
        $this->file_service = $file_service;
        // Data
        $this->data_extractor = $data_extractor;
        $this->table_converter = $table_converter;
        $this->data_restructure = $data_restructure;
        // Header
        $this->header_extractor = $header_extractor;
        $this->header_table_converter = $header_table_converter;
        $this->header_restructure = $header_restructure;

        // Customer group
        $this->customer_group_id = $customer_group_id;

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
            'arraymappingbymergeindex' => new MergeIndexArrayMappingRestructure(),
            'arraymappingbysearchtext' => new SearchTextArrayMappingRestructure(),
        ];
        // Header
        $this->header_extractor_instances = [
            'camelot' => new CamelotExtractorService(),
        ];
        $this->header_table_converter_instances = [
            'regexmatch' => new RegexMatchConverter(),
            'regexsplit' => new RegexSplitConverter(),
            'leaguecsv' => new LeagueCsvConverter(),
            'manual' => new ManualConverter(),
        ];
        $this->header_restructure_instances = [
            'arraymappingbyindex' => new IndexArrayMappingRestructure(),
            'arraymappingbykey' => new KeyArrayMappingRestructure(),
            'arraymappingbymergeindex' => new MergeIndexArrayMappingRestructure(),
            'arraymappingbysearchtext' => new SearchTextArrayMappingRestructure(),
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

    public function extractOrderDirect()
    {
        try {
            $files = $this->request->file('file');
            $final_data = array();
            foreach ($files as $file) {
                $array_data = array();
                $convert_file_type = null;
                $extract_data_config = null;
                $convert_table_config = null;
                $restructure_data_config = null;
                $config_id = $this->request->config_id;
                $extract_config = ExtractOrderConfig::find(intval($config_id));
                if ($extract_config) {
                    $convert_file_type = $extract_config->convert_file_type;
                    $extract_data_config = $extract_config->extract_data_config;
                    $convert_table_config =  $extract_config->convert_table_config;
                    $restructure_data_config = $extract_config->restructure_data_config;
                    $header_extractor_config = $extract_config->extract_header_config;
                    $header_convert_table_config =  $extract_config->convert_table_header_config;
                    $header_restructure_config = $extract_config->restructure_header_config;
                } else {
                    return null;
                }
                // Raw data
                $raw_data = $this->extractDataForDirect($file, $extract_data_config);
                $raw_header = $this->extractHeaderForDirect($file, $header_extractor_config);

                $order_data = null;
                $order_header = null;
                // Table data & order data
                if ($convert_file_type == 'pdf') {
                    // Data
                    $table_data = $this->convertToTableDirect($raw_data, $convert_table_config);
                    $order_data = $this->restructureDataDirect($table_data, $restructure_data_config);
                    // Header
                    $table_header = $this->convertHeaderToTableDirect($raw_header, $header_convert_table_config);
                    $restruct_header = $this->restructureHeaderDirect($table_header, $header_restructure_config);
                    $order_header = $this->addCustomInfo($restruct_header);

                    $array_data = [
                        'headers' => $order_header,
                        'items' => $order_data,
                    ];
                    array_push($final_data, $array_data);

                } else if ($convert_file_type == 'excel') {
                    // Data
                    $order_data = $this->restructureDataDirect($raw_data, $restructure_data_config);

                    $table_area_info = json_decode($extract_data_config->table_area_info);
                    $header_item_type = isset($table_area_info->header_item_type) ? $table_area_info->header_item_type : '';
                    $header_key_names = isset($table_area_info->header_key_names) ? $table_area_info->header_key_names : [];

                    switch ($header_item_type) {
                        case 'header-item':
                            $array_data = $this->restructureHeaderItem($order_data, $header_key_names);
                            $final_data = array_merge($final_data, $array_data);
                            break;
                        case 'header-items':
                            # code...
                            break;
                        case 'item-headers':
                            # code...
                            break;

                        default:
                            // Header
                            $restruct_header = $this->restructureHeaderDirect($raw_header, $header_restructure_config);
                            $order_header = $this->addCustomInfo($restruct_header);

                            $array_data = [
                                'headers' => $order_header,
                                'items' => $order_data,
                            ];
                            array_push($final_data, $array_data);
                            break;
                    }
                }
            }
            return $final_data;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function extractOrderFromUploadedFile($file_id)
    {
        try {
            $file_record = UploadedFile::query()->with(['batch', 'batch.customer.group', 'batch.warehouse'])->find($file_id);
            if (!$file_record) {
                $this->message = 'File không tồn tại';
                return false;
            }
            $processing_status = FileStatus::query()->where('code', FileStatuses::PROCESSING)->first();
            $file_record->status_id = $processing_status->id;
            $file_record->save();
            $file_path = Storage::disk('protected')->path($file_record->path);
            $extract_order_config = ExtractOrderConfig::query()->with([
                'extract_data_config', 'convert_table_config', 'restructure_data_config',
                'extract_header_config', 'convert_table_header_config', 'restructure_header_config'
            ])
                ->find($file_record->batch->extract_order_config_id);

            $final_data = $this->extractRawDataFromUploadedFile($file_record, $file_path, $extract_order_config);
            $final_header = $extract_order_config->is_convert_header ?
                $this->extractHeaderFromUploadedFile($file_record, $file_path, $extract_order_config) :
                [];

            DB::beginTransaction();
            $customer_group = $file_record->batch->customer->group;
            $created_extract_items = new  \Illuminate\Database\Eloquent\Collection([]);
            $error_extract_items = [];

            $po_number = isset($final_header['PoNumber']) ? $final_header['PoNumber'] : '';
            $po_person = isset($final_header['PoPerson']) ? $final_header['PoPerson'] : '';
            $po_phone = isset($final_header['PoPhone']) ? $final_header['PoPhone'] : '';
            $po_delivery_address = isset($final_header['PoDeliveryAddress']) ? $final_header['PoDeliveryAddress'] : '';
            $po_note = isset($final_header['PoNote']) ? $final_header['PoNote'] : '';
            $po_email = isset($final_header['PoEmail']) ? $final_header['PoEmail'] : '';
            $po_delivery_date = isset($final_header['PoDeliveryDate']) ? $final_header['PoDeliveryDate'] : null;
            $po_delivery_date = $po_delivery_date ? $po_delivery_date : null;
            $po_date = isset($final_header['PoDate']) ? $final_header['PoDate'] : null;
            $po_date = $po_date ? $po_date : null;

            $raw_extract_header = RawExtractHeader::firstOrCreate(
                [
                    'customer_id' => $file_record->batch->customer_id,
                    'uploaded_file_id' => $file_record->id,
                    'po_number' => $po_number,
                    'po_date' => $po_date,
                    'po_person' => $po_person,
                    'po_phone' => $po_phone,
                    'po_delivery_address' => $po_delivery_address,
                    'po_delivery_date' => $po_delivery_date,
                    'po_note' => $po_note,
                ]
            );
            foreach ($final_data as $item) {

                if (!isset($item['ProductID']) || $item['ProductID'] == '') {
                    continue;
                }
                $customer_material = CustomerMaterial::query()
                    ->where('customer_group_id', $customer_group->id)
                    ->where('customer_sku_code', $item['ProductID'])
                    // ->where('customer_sku_name', $item['ProductName'])
                    ->first();
                if (!$customer_material) {
                    $error_extract_items[] = 'Không tìm thấy customer material với ProductID: ' . $item['ProductID'] . ' của customer group: ' . $customer_group->name;
                    continue;
                }

                $raw_extract_item = RawExtractItem::firstOrCreate([
                    'raw_extract_header_id' => $raw_extract_header->id,
                    'customer_material_id' => $customer_material->id,
                    'quantity' => $item['Quantity'],
                    'price' =>  str_replace(",", "", $item['ProductPrice']),
                    'amount' => str_replace(",", "", $item['ProductAmount']),
                ]);
                $created_extract_items->push($raw_extract_item);
            }
            if (count($error_extract_items) > 0) {
                $error_log = json_encode($error_extract_items);
                throw new NotFoundCustomerMaterialException($file_record->id, $error_log);
            }

            $created_so_items_by_warehouse = collect();
            $created_so_items = collect([]);
            $error_so_items = [];

            $raw_so_header = RawSoHeader::firstOrCreate(
                array_merge(
                    $raw_extract_header->toArray(),
                    [
                        'raw_extract_header_id' => $raw_extract_header->id,
                        // 'po_person' => $file_record->batch->customer->name,
                        // 'po_phone' => $file_record->batch->customer->phone_number,
                        // 'po_email' => $po_email ? $po_email : $file_record->batch->customer->email,
                        // 'po_delivery_address' => $file_record->batch->customer->address,
                    ]
                )
            );
            if (!$raw_so_header->serial_number) {
                $raw_so_header->serial_number = UniqueIdUtility::generateSerialUniqueNumber($file_record->batch->customer->code);
                $raw_so_header->save();
            }

            $created_so_items = collect();
            $created_promotion_items = [];

            $created_extract_items->load(['customer_material.mappings.sap_material']);

            foreach ($created_extract_items as $item) {

                $sap_material_mappings = $item->customer_material->mappings;
                Log::info("sap_material_mappings");
                Log::info($sap_material_mappings);
                if (count($sap_material_mappings) == 0) {
                    $error_so_items[] = 'Không tìm thấy sap material với customer material code: ' . $item->customer_material->customer_sku_code . ' (' . $item->customer_material->customer_sku_name . ')';
                    continue;
                }

                foreach ($sap_material_mappings as $mapping) {
                    $sap_material = $mapping->sap_material;
                    $quantity = round(($item->quantity) * ($mapping->percentage / 100), 0);
                    $price = $item->price;
                    $amount = $quantity * $item->price;
                    $raw_so_item = RawSoItem::firstOrCreate([
                        'raw_extract_item_id' => $item->id,
                        'raw_so_header_id' => $raw_so_header->id,
                        'sap_material_id' => $sap_material->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'amount' => $amount,
                        'percentage' => $mapping->percentage,
                        'warehouse_id' => $file_record->batch->warehouse->id,
                    ]);
                    $created_so_items->push($raw_so_item);
                    $customer_promotion = CustomerPromotion::where('sap_material_id', $sap_material->id)->first();
                    if ($customer_promotion) {
                        $created_promotion_items[] = clone $raw_so_item;
                    }
                }
                if ($created_so_items->isNotEmpty()) {
                    $created_so_items_by_warehouse = $created_so_items->groupBy('warehouse_id');
                }
                $extract_item_quantity = $item->quantity;
                $so_items_quantity = $item->raw_so_items->sum('quantity');
                if ($extract_item_quantity < $so_items_quantity) {
                    $max_quantity_so_item_index = array_keys($item->raw_so_items->toArray(), max($item->raw_so_items->toArray()))[0];
                    $max_quantity_so_item = $item->raw_so_items[$max_quantity_so_item_index];
                    $max_quantity_so_item->quantity = $max_quantity_so_item->quantity - ($so_items_quantity - $extract_item_quantity);
                } elseif ($extract_item_quantity > $so_items_quantity) {
                    $min_quantity_so_item_index = array_keys($item->raw_so_items->toArray(), min($item->raw_so_items->toArray()))[0];
                    $min_quantity_so_item = $item->raw_so_items[$min_quantity_so_item_index];
                    $min_quantity_so_item->quantity = $min_quantity_so_item->quantity + ($extract_item_quantity - $so_items_quantity);
                }
            }
            if (count($error_so_items) > 0) {
                $error_log = json_encode($error_so_items);
                throw new NotFoundSapMaterialException($file_record->id, $error_log);
            }
            $converted_status = FileStatus::query()->where('code', FileStatuses::CONVERTED)->first();
            $file_record->status_id = $converted_status->id;
            $file_record->save();
            //Tao đơn hàng KM
            if (!empty($created_promotion_items)) {
                $raw_so_header_promotion = RawSoHeader::firstOrCreate(
                    array_merge(
                        $raw_extract_header->toArray(),
                        [
                            'raw_extract_header_id' => $raw_so_header->raw_extract_header_id,
                            // 'po_person' => $file_record->batch->customer->name,
                            // 'po_phone' => $file_record->batch->customer->phone_number,
                            // 'po_email' => $po_email ? $po_email : $file_record->batch->customer->email,
                            // 'po_delivery_address' => $file_record->batch->customer->address,
                            'note' => 'Đơn hàng khuyến mãi'
                        ]
                    )
                );
                if (!$raw_so_header_promotion->serial_number) {
                    $raw_so_header_promotion->serial_number = UniqueIdUtility::generateSerialUniqueNumber($file_record->batch->customer->code);
                    $raw_so_header_promotion->save();
                }
                foreach ($created_promotion_items as $promotion_itm) {
                    $raw_so_item = RawSoItem::firstOrCreate([
                        'raw_extract_item_id' => $promotion_itm->raw_extract_item_id,
                        'raw_so_header_id' => $raw_so_header_promotion->id,
                        'sap_material_id' => $promotion_itm->sap_material_id,
                        'quantity' =>  '1',
                        'price' => $promotion_itm->price,
                        'amount' => ($promotion_itm->quantity * $promotion_itm->quantity),
                        'percentage' => '100',
                        'warehouse_id' => $file_record->batch->warehouse->id,

                    ]);
                }
            }
            DB::commit();
            return array(
                'created_extract_items' => $created_extract_items,
                'error_extract_items' => $error_extract_items,
                'created_so_items' => $created_so_items,
                'error_so_items' => $error_so_items,
                'created_extract_items_count' => count($created_extract_items),
                'created_so_items_count' => count($created_so_items),
                'created_so_items_by_warehouse' => $created_so_items_by_warehouse,
            );
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            $error_status = FileStatus::query()->where('code', FileStatuses::ERROR)->first();
            $file_record->status_id = $error_status->id;
            $file_record->save();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function reconvertUploadedFile($file_id)
    {
        try {
            DB::beginTransaction();
            $file = UploadedFile::query()->with(['batch.extract_order_config'])->find($file_id);
            $reconvert_status = FileStatus::query()->where('code', FileStatuses::RECONVERT)->first();
            $file->status_id = $reconvert_status->id;
            $file->save();
            if (!$file) {
                $this->message = 'File không tồn tại';
                return;
            }
            $this->request->merge([
                'extract_order_config' => $file->batch->extract_order_config->reference_id,
                'customer' => $file->batch->customer_id,
                'company' => $file->batch->company_code,
                'warehouse' => $file->batch->warehouse_id,
            ]);
            $uploaded_file_repository = new UploadedFileRepository($this->request);
            $batch_id = $uploaded_file_repository->prepareUploadFile();
            $batch = Batch::query()->find($batch_id);
            $batch->reference_batch_id = $file->batch->id;
            $batch->save();

            $replicate_file = $file->replicate();
            $replicate_file->batch_id = $batch_id;
            $replicate_file->reference_file_id = $file->id;
            $replicate_file->save();

            $owners = $file->user_morphs->pluck('user_id')->toArray();
            // dd($owners);
            foreach ($owners as $user_id) {
                // $user_morph = new UserMorph(['user_id' => $user_id]);
                $replicate_file->user_morphs()->create(['user_id' => $user_id]);
            }

            $file->delete();

            ExtractFile::dispatch($replicate_file->id);

            DB::commit();
            $replicate_file->load(['batch.customer.group', 'raw_so_headers', 'status']);
            return $replicate_file;
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    private function extractRawDataFromUploadedFile($file_record, $file_path, $extract_order_config)
    {
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

        return $final_data;
    }

    private function extractHeaderFromUploadedFile($file_record, $file_path, $extract_order_config)
    {
        try {
            $this->data_extractor = $this->data_extractor_instances[$extract_order_config->extract_header_config->method];
            $raw_data = $this->extractData($file_path, $extract_order_config->extract_header_config);
        } catch (\Throwable $exception) {
            $extract_error = ExtractError::query()->where('code', ExtractErrorsEnum::EXTRACT_ERROR)->first();
            FileExtractError::create([
                'uploaded_file_id' => $file_record->id,
                'extract_error_id' => $extract_error->id,
            ]);
            throw $exception;
        }

        try {
            $this->table_converter = $this->table_converter_instances[$extract_order_config->convert_table_header_config->method];
            $table_data = $this->convertToTable($raw_data, $extract_order_config->convert_table_header_config);
        } catch (\Throwable $exception) {
            $convert_error = ExtractError::query()->where('code', ExtractErrorsEnum::CONVERT_ERROR)->first();
            FileExtractError::create([
                'uploaded_file_id' => $file_record->id,
                'extract_error_id' => $convert_error->id,
            ]);
            throw $exception;
        }

        try {
            $this->data_restructure = $this->data_restructure_instances[$extract_order_config->restructure_header_config->method];
            $final_data = $this->restructureData($table_data, $extract_order_config->restructure_header_config);
        } catch (\Throwable $exception) {
            $restructure_error = ExtractError::query()->where('code', ExtractErrorsEnum::RESTRUCTURE_ERROR)->first();
            FileExtractError::create([
                'uploaded_file_id' => $file_record->id,
                'extract_error_id' => $restructure_error->id,
            ]);
            throw $exception;
        }

        return $final_data;
    }
    private function extractData($file_path, $extract_data_config = null)
    {
        $options = array();
        if ($this->data_extractor instanceof CamelotExtractorService) {
            if (!$extract_data_config) {
                $options['is_merge_pages'] = $this->request->is_merge_pages == 'true' ? true : false;
                $options['flavor'] = $this->request->camelot_flavor ? $this->request->camelot_flavor : 'lattice'; // Lưu trữ 'stream' hoặc 'lattice' với từng trường hợp
                $exclude_head_tables_count = $this->request->exclude_head_tables_count ? $this->request->exclude_head_tables_count : 0;
                $exclude_tail_tables_count = $this->request->exclude_tail_tables_count ? $this->request->exclude_tail_tables_count : 0;
                $specify_table_number = $this->request->specify_table_number ? $this->request->specify_table_number : 0;
                $options['is_specify_table_area'] = $this->request->is_specify_table_area == 'true' ? true : false;
                $table_area_info = json_decode($this->request->table_area_info);
                $options['table_area_info'] = $table_area_info;
                $options['is_specify_advanced_settings'] = $this->request->is_specify_advanced_settings == 'true' ? true : false;
                $advanced_settings_info = json_decode($this->request->advanced_settings_info);
                $options['advanced_settings_info'] = $advanced_settings_info;
            } else {
                $options['is_merge_pages'] = $extract_data_config->is_merge_pages ? $extract_data_config->is_merge_pages : false;
                $options['flavor'] = $extract_data_config->camelot_flavor ? $extract_data_config->camelot_flavor : 'lattice'; // Lưu trữ 'stream' hoặc 'lattice' với từng trường hợp
                $exclude_head_tables_count = $extract_data_config->exclude_head_tables_count ? $extract_data_config->exclude_head_tables_count : 0;
                $exclude_tail_tables_count = $extract_data_config->exclude_tail_tables_count ? $extract_data_config->exclude_tail_tables_count : 0;
                $specify_table_number = $extract_data_config->specify_table_number ? $extract_data_config->specify_table_number : 0;
                $options['is_specify_table_area'] = $extract_data_config->is_specify_table_area ? $extract_data_config->is_specify_table_area : false;
                $table_area_info = json_decode($extract_data_config->table_area_info);
                $options['table_area_info'] = $table_area_info;
                $options['is_specify_advanced_settings'] = $extract_data_config->is_specify_advanced_settings ? $extract_data_config->is_specify_advanced_settings : false;
                $advanced_settings_info = json_decode($extract_data_config->advanced_settings_info);
                $options['advanced_settings_info'] = $advanced_settings_info;
            }
        } else if ($this->data_extractor instanceof ExcelExtractorService) {
            if (!$extract_data_config) {
                $table_area_info = json_decode($this->request->table_area_info);
                $options['table_area_info'] = $table_area_info;
                $specify_table_number = 0;
                $exclude_head_tables_count = 0;
                $exclude_tail_tables_count = 0;
            } else {
                $table_area_info = json_decode($extract_data_config->table_area_info);
                $options['table_area_info'] = $table_area_info;
                $specify_table_number = 0;
                $exclude_head_tables_count = 0;
                $exclude_tail_tables_count = 0;
            }
        }
        $tables = $this->data_extractor->extract($file_path, $options);
        $choosen_tables = [];

        if ($specify_table_number > 0) {
            $choosen_tables[] = $tables[$specify_table_number - 1];
        } else {
            for ($i = $exclude_head_tables_count; $i < count($tables) - $exclude_tail_tables_count; $i++) {
                $choosen_tables[] = $tables[$i];
            }
        }
        return $choosen_tables;
    }
    private function extractHeader($file_path, $extract_header_config = null)
    {
        $options = array();
        if ($this->header_extractor instanceof CamelotExtractorService) {
            if (!$extract_header_config) {
                $options['is_merge_pages'] = $this->request->is_merge_pages == 'true' ? true : false;
                $options['flavor'] = $this->request->camelot_flavor ? $this->request->camelot_flavor : 'lattice'; // Lưu trữ 'stream' hoặc 'lattice' với từng trường hợp
                $exclude_head_tables_count = $this->request->exclude_head_tables_count ? $this->request->exclude_head_tables_count : 0;
                $exclude_tail_tables_count = $this->request->exclude_tail_tables_count ? $this->request->exclude_tail_tables_count : 0;
                $specify_table_number = $this->request->specify_table_number ? $this->request->specify_table_number : 0;
                $options['is_specify_table_area'] = $this->request->is_specify_table_area == 'true' ? true : false;
                $table_area_info = json_decode($this->request->table_area_info);
                $options['table_area_info'] = $table_area_info;
                $options['is_specify_advanced_settings'] = $this->request->is_specify_advanced_settings == 'true' ? true : false;
                $advanced_settings_info = json_decode($this->request->advanced_settings_info);
                $options['advanced_settings_info'] = $advanced_settings_info;
            } else {
                $options['is_merge_pages'] = $extract_header_config->is_merge_pages ? $extract_header_config->is_merge_pages : false;
                $options['flavor'] = $extract_header_config->camelot_flavor ? $extract_header_config->camelot_flavor : 'lattice'; // Lưu trữ 'stream' hoặc 'lattice' với từng trường hợp
                $exclude_head_tables_count = $extract_header_config->exclude_head_tables_count ? $extract_header_config->exclude_head_tables_count : 0;
                $exclude_tail_tables_count = $extract_header_config->exclude_tail_tables_count ? $extract_header_config->exclude_tail_tables_count : 0;
                $specify_table_number = $extract_header_config->specify_table_number ? $extract_header_config->specify_table_number : 0;
                $options['is_specify_table_area'] = $extract_header_config->is_specify_table_area ? $extract_header_config->is_specify_table_area : false;
                $table_area_info = json_decode($extract_header_config->table_area_info);
                $options['table_area_info'] = $table_area_info;
                $options['is_specify_advanced_settings'] = $extract_header_config->is_specify_advanced_settings ? $extract_header_config->is_specify_advanced_settings : false;
                $advanced_settings_info = json_decode($extract_header_config->advanced_settings_info);
                $options['advanced_settings_info'] = $advanced_settings_info;
            }
        } else if ($this->data_extractor instanceof ExcelExtractorService) {
            if (!$extract_header_config) {
                $table_area_info = json_decode($this->request->table_area_info);
                $options['table_area_info'] = $table_area_info;
                $specify_table_number = 0;
                $exclude_head_tables_count = 0;
                $exclude_tail_tables_count = 0;
            } else {
                $table_area_info = json_decode($extract_header_config->table_area_info);
                $options['table_area_info'] = $table_area_info;
                $specify_table_number = 0;
                $exclude_head_tables_count = 0;
                $exclude_tail_tables_count = 0;
            }
        }
        $tables = $this->header_extractor->extract($file_path, $options);
        $choosen_tables = [];
        if ($specify_table_number > 0) {
            $choosen_tables[] = $tables[$specify_table_number - 1];
        } else {
            for ($i = $exclude_head_tables_count; $i < count($tables) - $exclude_tail_tables_count; $i++) {
                $choosen_tables[] = $tables[$i];
            }
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
            if (!$convert_table_config) {
                $options['is_without_header'] = $this->request->is_without_header == 'true' ? true : false;
                $manual_patterns = json_decode($this->request->manual_patterns);
                $options['manual_patterns'] = $manual_patterns;
            } else {
                $options['is_without_header'] = $convert_table_config->is_without_header ? $convert_table_config->is_without_header : false;
                $manual_patterns = json_decode($convert_table_config->manual_patterns);
                $options['manual_patterns'] = $manual_patterns;
            }
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
        // Merge các array theo số lượng được config
        if (isset($options['manual_patterns'])) {
            $manual_patterns = $options['manual_patterns'];
            if (isset($manual_patterns->step4->merge_array)) {
                $merge_array = $manual_patterns->step4->merge_array;
                if (isset($merge_array->number_array)) {
                    $number_array = $merge_array->number_array;
                    $table = collect($table)->chunk($number_array)->map(function ($item) {
                        return $item->flatten();
                    });
                }
            }
        }
        return $table;
    }

    private function convertHeaderToTable($array, $convert_table_config = null)
    {
        $options = array();
        if ($this->header_table_converter instanceof RegexMatchConverter) {
            if (!$convert_table_config) {
                $options['regex_pattern'] = $this->request->regex_pattern;
            } else {
                $options['regex_pattern'] = $convert_table_config->regex_pattern;
            }
        } elseif ($this->header_table_converter instanceof RegexSplitConverter) {
            if (!$convert_table_config) {
                $options['regex_pattern'] = $this->request->regex_pattern;
            } else {
                $options['regex_pattern'] = $convert_table_config->regex_pattern;
            }
        } elseif ($this->header_table_converter instanceof LeagueCsvConverter) {
            if (!$convert_table_config) {
                $options['is_without_header'] = $this->request->is_without_header == 'true' ? true : false;
                $manual_patterns = json_decode($this->request->manual_patterns);
                $options['manual_patterns'] = $manual_patterns;
            } else {
                $options['is_without_header'] = $convert_table_config->is_without_header ? $convert_table_config->is_without_header : false;
                $manual_patterns = json_decode($convert_table_config->manual_patterns);
                $options['manual_patterns'] = $manual_patterns;
            }
        } elseif ($this->header_table_converter instanceof ManualConverter) {
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
            $extracted_table = $this->header_table_converter->convert($array[$i], $options);
            $table = array_merge($table, $extracted_table);
        }
        // Merge các array theo số lượng được config
        if (isset($options['manual_patterns'])) {
            $manual_patterns = $options['manual_patterns'];
            if (isset($manual_patterns->step4->merge_array)) {
                $merge_array = $manual_patterns->step4->merge_array;
                if (isset($merge_array->number_array)) {
                    $number_array = $merge_array->number_array;
                    $table = collect($table)->chunk($number_array)->map(function ($item) {
                        return $item->flatten();
                    });
                }
            }
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
        } elseif ($this->data_restructure instanceof MergeIndexArrayMappingRestructure) {
            if (!$restructure_data_config) {
                $options['structure'] = json_decode($this->request->structure, true);
            } else {
                $options['structure'] = json_decode($restructure_data_config->structure, true);
            }
        } elseif ($this->data_restructure instanceof SearchTextArrayMappingRestructure) {
            if (!$restructure_data_config) {
                $options['structure'] = json_decode($this->request->structure, true);
            } else {
                $options['structure'] = json_decode($restructure_data_config->structure, true);
            }
        }
        $table = $this->data_restructure->restructure($array, $options);
        return $table;
    }

    private function restructureHeader($array, $restructure_data_config = null)
    {
        $options = array();
        if ($this->header_restructure instanceof IndexArrayMappingRestructure) {
            if (!$restructure_data_config) {
                $options['structure'] = json_decode($this->request->structure, true);
            } else {
                $options['structure'] = json_decode($restructure_data_config->structure, true);
            }
        } elseif ($this->header_restructure instanceof KeyArrayMappingRestructure) {
            if (!$restructure_data_config) {
                $options['structure'] = json_decode($this->request->structure, true);
            } else {
                $options['structure'] = json_decode($restructure_data_config->structure, true);
            }
        } elseif ($this->header_restructure instanceof MergeIndexArrayMappingRestructure) {
            if (!$restructure_data_config) {
                $options['structure'] = json_decode($this->request->structure, true);
            } else {
                $options['structure'] = json_decode($restructure_data_config->structure, true);
            }
        } elseif ($this->header_restructure instanceof SearchTextArrayMappingRestructure) {
            if (!$restructure_data_config) {
                $options['structure'] = json_decode($this->request->structure, true);
            } else {
                $options['structure'] = json_decode($restructure_data_config->structure, true);
            }
        }
        $table = $this->header_restructure->restructure($array, $options);

        return $table;
    }

    private function addCustomInfo($table_data)
    {
        // Thêm thông tin customer partner
        if (isset( $table_data['CustomerKey'])) {
            $customer_key = trim($table_data['CustomerKey']);
            $customer_partner = CustomerPartner::query()->where('customer_group_id', $this->customer_group_id)->where('name', $customer_key)->first();
            if ($customer_partner) {
                $table_data['CustomerCode'] = $customer_partner->code;
                $table_data['CustomerNote'] = $customer_partner->note;
                $table_data['CustomerLevel2'] = $customer_partner->LV2;
                $table_data['CustomerLevel3'] = $customer_partner->LV3;
                $table_data['CustomerLevel4'] = $customer_partner->LV4;

            } else {
                $table_data['CustomerCode'] = null;
                $table_data['CustomerNote'] = null;
                $table_data['CustomerLevel2'] = null;
                $table_data['CustomerLevel3'] = null;
                $table_data['CustomerLevel4'] = null;
            }
        }
        // Thêm trường SapSoNumber
        if (isset( $table_data['PoNumber']) && isset( $table_data['PoDeliveryDate'])) {
            $po_delivery_date = str_replace('-', '', $table_data['PoDeliveryDate']);
            $table_data['SapSoNumber'] = $po_delivery_date ? $table_data['PoNumber'] . '-' . $po_delivery_date
                : $table_data['PoNumber'];
        }
        return $table_data;
    }
    private function restructureHeaderItem($order_data, $header_key_names) {
        $result = array();
        foreach ($order_data as $data) {
            $items = array();
            $header = array();
            // Data
            $items[] = array_diff_key($data, array_flip($header_key_names));
            // Header
            foreach ($header_key_names as $header_key) {
                $header[$header_key] = $data[$header_key];
            }
            $header = $this->addCustomInfo($header);

            $array_data = [
                'headers' => $header,
                'items' => $items,
            ];
            array_push($result, $array_data);
        }
        return $result;
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

    public function extractDataForDirect($file, $extract_data_config)
    {
        try {
            // $file = $this->request->file('file');
            $file_path = $this->file_service->saveTemporaryFile($file);
            $raw_data = $this->extractData($file_path, $extract_data_config);
            $this->file_service->deleteTemporaryFile($file_path);
            return $raw_data;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function extractHeaderForDirect($file, $extract_header_config)
    {
        try {
            $file_path = $this->file_service->saveTemporaryFile($file);
            $raw_data = $this->extractHeader($file_path, $extract_header_config);
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

    public function convertToTableDirect($raw_table_data, $convert_table_config)
    {
        try {
            // $raw_table_data = json_decode($raw_table_data, true);

            return $this->convertToTable($raw_table_data, $convert_table_config);
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function convertHeaderToTableDirect($raw_table_data, $convert_table_config)
    {
        try {
            return $this->convertHeaderToTable($raw_table_data, $convert_table_config);
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function restructureDataForConfig()
    {
        try {
            $table_data = json_decode($this->data['table_data'], true);
            $restruct_data = $this->restructureData($table_data);
            return $this->addCustomInfo($restruct_data);
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function restructureDataDirect($table_data, $restructure_data_config)
    {
        try {
            // $table_data = json_decode($table_data, true);
            return $this->restructureData($table_data, $restructure_data_config);
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function restructureHeaderDirect($table_data, $restructure_data_config)
    {
        try {
            return $this->restructureHeader($table_data, $restructure_data_config);
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

        $query->with([
            'extract_data_config', 'convert_table_config', 'restructure_data_config',
            'extract_header_config', 'convert_table_header_config', 'restructure_header_config'
        ]);
        $extract_order_configs = $query->get();
        return $extract_order_configs;
    }

    public function createExtractOrderConfigs()
    {
        try {
            $validator = Validator::make($this->data, [
                'convert_file_type' => 'required',
                'customer_group_id' => 'required|exists:customer_groups,id',
                'extract_data_config' => 'required',
                'convert_table_config' => 'required',
                'restructure_data_config' => 'required',
                'extract_header_config' => 'required',
                'convert_table_header_config' => 'required',
                'restructure_header_config' => 'required',
                'name' => 'required|unique:extract_order_configs,name',
            ], [
                'convert_file_type.required' => 'Convert file type là bắt buộc',
                'customer_group_id.required' => 'Customer group là bắt buộc',
                'customer_group_id.exists' => 'Customer group không tồn tại',
                'extract_data_config.required' => 'Extract data config là bắt buộc',
                'extract_data_config.json' => 'Extract data config không đúng định dạng json',
                'convert_table_config.required' => 'Convert table config là bắt buộc',
                'convert_table_config.json' => 'Convert table config không đúng định dạng json',
                'restructure_data_config.required' => 'Restructure data config là bắt buộc',
                'restructure_data_config.json' => 'Restructure data config không đúng định dạng json',
                'extract_header_config.required' => 'Extract header config là bắt buộc',
                'extract_header_config.json' => 'Extract header config không đúng định dạng json',
                'convert_table_header_config.required' => 'Convert table header config là bắt buộc',
                'convert_table_header_config.json' => 'Convert table header config không đúng định dạng json',
                'restructure_header_config.required' => 'Restructure header config là bắt buộc',
                'restructure_header_config.json' => 'Restructure header config không đúng định dạng json',
                'name.required' => 'Tên là bắt buộc',
                'name.unique' => 'Tên đã tồn tại',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();

                // $this->data['extract_data_config']['table_area_info'] = json_encode($this->data['extract_data_config']['table_area_info']);
                $extract_data_config = ExtractDataConfig::create($this->data['extract_data_config']);

                // $this->data['convert_table_config']['manual_patterns'] = json_encode($this->data['convert_table_config']['manual_patterns']);
                $convert_table_config = ConvertTableConfig::create($this->data['convert_table_config']);

                // $this->data['restructure_data_config']['structure'] = json_encode($this->data['restructure_data_config']['structure']);
                $restructure_data_config = RestructureDataConfig::create($this->data['restructure_data_config']);

                // $this->data['extract_header_config']['table_area_info'] = json_encode($this->data['extract_header_config']['table_area_info']);
                $extract_header_config = ExtractDataConfig::create($this->data['extract_header_config']);

                // $this->data['convert_table_header_config']['manual_patterns'] = json_encode($this->data['convert_table_header_config']['manual_patterns']);
                $convert_table_header_config = ConvertTableConfig::create($this->data['convert_table_header_config']);

                // $this->data['restructure_header_config']['structure'] = json_encode($this->data['restructure_header_config']['structure']);
                $restructure_header_config = RestructureDataConfig::create($this->data['restructure_header_config']);

                $is_convert_header = $this->data['is_convert_header'] == 'true' ? true : false;
                $convert_file_type = $this->data['convert_file_type'];
                $is_official = true;
                $active = $this->data['active'] == 'true' ? true : false;

                $extract_order_config = ExtractOrderConfig::create([
                    'name' => $this->data['name'],
                    'customer_group_id' => $this->data['customer_group_id'],
                    'extract_data_config_id' => $extract_data_config->id,
                    'convert_table_config_id' => $convert_table_config->id,
                    'restructure_data_config_id' => $restructure_data_config->id,
                    'extract_header_config_id' => $extract_header_config->id,
                    'convert_table_header_config_id' => $convert_table_header_config->id,
                    'restructure_header_config_id' => $restructure_header_config->id,
                    'is_convert_header' => $is_convert_header,
                    'convert_file_type' => $convert_file_type,
                    'is_official' => $is_official,
                    'active' => $active,
                ]);

                DB::commit();
                $extract_order_config->load([
                    'extract_data_config', 'convert_table_config', 'restructure_data_config',
                    'extract_header_config', 'convert_table_header_config', 'restructure_header_config'
                ]);
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
            $data_config_type = $this->data['data_config_type'];
            if ($data_config_type == 'data') {
                $validator = Validator::make($this->data, [
                    'extract_data_config' => 'required',
                    'convert_table_config' => 'required',
                    'restructure_data_config' => 'required',
                ], [
                    'extract_data_config.required' => 'Extract data config là bắt buộc',
                    'convert_table_config.required' => 'Convert table config là bắt buộc',
                    'restructure_data_config.required' => 'Restructure data config là bắt buộc',
                ]);
            } elseif ($data_config_type == 'header') {
                $validator = Validator::make($this->data, [
                    'extract_header_config' => 'required',
                    'convert_table_header_config' => 'required',
                    'restructure_header_config' => 'required',
                ], [
                    'extract_header_config.required' => 'Extract header config là bắt buộc',
                    'convert_table_header_config.required' => 'Convert table header config là bắt buộc',
                    'restructure_header_config.required' => 'Restructure header config là bắt buộc',
                ]);
            }

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                if ($data_config_type == 'data') {
                    // $this->data['extract_data_config']['table_area_info'] = json_encode($this->data['extract_data_config']['table_area_info']);
                    // $this->data['convert_table_config']['manual_patterns'] = json_encode($this->data['convert_table_config']['manual_patterns']);
                    // $this->data['restructure_data_config']['structure'] = json_encode($this->data['restructure_data_config']['structure']);

                    $extract_order_config = ExtractOrderConfig::query()->with(['extract_data_config', 'convert_table_config', 'restructure_data_config'])->find($extract_order_config_id);
                    if (!$extract_order_config) {
                        $this->message = 'Extract order config không tồn tại';
                        return;
                    }

                    if ($extract_order_config->extract_data_config) {
                        $extract_order_config->extract_data_config->update($this->data['extract_data_config']);
                    } else {
                        $extract_data_config = ExtractDataConfig::create($this->data['extract_data_config']);
                        $extract_order_config->extract_data_config_id = $extract_data_config->id;
                    }
                    if ($extract_order_config->convert_table_config) {
                        $extract_order_config->convert_table_config->update($this->data['convert_table_config']);
                    } else {
                        $convert_table_config = ConvertTableConfig::create($this->data['convert_table_config']);
                        $extract_order_config->convert_table_config_id = $convert_table_config->id;
                    }
                    if ($extract_order_config->restructure_data_config) {
                        $extract_order_config->restructure_data_config->update($this->data['restructure_data_config']);
                    } else {
                        $restructure_data_config = RestructureDataConfig::create($this->data['restructure_data_config']);
                        $extract_order_config->restructure_data_config_id = $restructure_data_config->id;
                    }
                    $extract_order_config->save();
                } elseif ($data_config_type == 'header') {
                    // $this->data['extract_header_config']['table_area_info'] = json_encode($this->data['extract_header_config']['table_area_info']);
                    // $this->data['convert_table_header_config']['manual_patterns'] = json_encode($this->data['convert_table_header_config']['manual_patterns']);
                    // $this->data['restructure_header_config']['structure'] = json_encode($this->data['restructure_header_config']['structure']);
                    $this->data['is_convert_header'] = $this->data['is_convert_header'] == 'true' ? true : false;

                    $extract_order_config = ExtractOrderConfig::query()->with(['extract_header_config', 'convert_table_header_config', 'restructure_header_config'])->find($extract_order_config_id);
                    if (!$extract_order_config) {
                        $this->message = 'Extract order config không tồn tại';
                        return;
                    }
                    $extract_order_config->is_convert_header = $this->data['is_convert_header'];

                    if ($extract_order_config->extract_header_config) {
                        $extract_order_config->extract_header_config->update($this->data['extract_header_config']);
                    } else {
                        $extract_header_config = ExtractDataConfig::create($this->data['extract_header_config']);
                        $extract_order_config->extract_header_config_id = $extract_header_config->id;
                    }
                    if ($extract_order_config->convert_table_header_config) {
                        $extract_order_config->convert_table_header_config->update($this->data['convert_table_header_config']);
                    } else {
                        $convert_table_header_config = ConvertTableConfig::create($this->data['convert_table_header_config']);
                        $extract_order_config->convert_table_header_config_id = $convert_table_header_config->id;
                    }
                    if ($extract_order_config->restructure_header_config) {
                        $extract_order_config->restructure_header_config->update($this->data['restructure_header_config']);
                    } else {
                        $restructure_header_config = RestructureDataConfig::create($this->data['restructure_header_config']);
                        $extract_order_config->restructure_header_config_id = $restructure_header_config->id;
                    }
                    $extract_order_config->save();
                }

                DB::commit();
                return $extract_order_config;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
