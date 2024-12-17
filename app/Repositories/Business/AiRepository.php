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
use App\Services\Implementations\Extractors\PdfTextLocatorService;
use App\Services\Implementations\Extractors\AiExtractorService;

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
use App\Jobs\SaveRawPoData;
use App\Models\Business\FileExtractError;
use App\Models\Business\FileStatus;
use App\Models\Business\RawSoHeader;
use App\Models\Business\RawSoItem;
use App\Models\Master\Warehouse;
use App\Utilities\UniqueIdUtility;
use Exception;
use Illuminate\Http\Request;
use App\Enums\Ai\Convert\ConvertMethod;
use App\Enums\Ai\Extract\ExtractMethod;
use App\Enums\Ai\Restructure\RestructureMethod;
use App\Models\Master\SapMaterial;
use App\Models\Business\RawPoHeader;
use App\Models\Business\RawPoDataItem;
use App\Models\Business\PoUploadFile;

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
        $file_service = $this->file_service;
        $request = $this->request;
        $file_path = null;
        $final_data = null;

        try {
            $file = $request->file('file');
            $file_path = $file_service->saveTemporaryFile($file);
            $raw_data = $this->extractData($file_path);
            $table_data = $this->convertToTable($raw_data);
            $final_data = $this->restructureData($table_data);
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        } finally {
            if (isset($file_path)) {
                $file_service->deleteTemporaryFile($file_path);
            }
        }

        return $final_data;
    }

    public function extractOrderDirect()
    {
        try {
            $files = $this->request->file('file');
            $final_data = array();
            $csv_data = array();
            $file = $files[0]; // Chỉ xử lý 1 file
            $file_name = $file->getClientOriginalName();
            try {
                $array_data = array();
                $convert_file_type = null;
                $extract_data_config = null;
                $convert_table_config = null;
                $restructure_data_config = null;
                $config_id = $this->request->config_id;
                $extract_config = ExtractOrderConfig::find(intval($config_id));
                $mapping_config = null;
                // Xét extract_config thuộc dạng nhóm config hay config đơn lẻ
                if ($extract_config) {
                    if ($extract_config->is_config_group) {
                        $is_mapping_config = false;
                        // Kiểm tra config chính
                        $is_mapping_config = $this->checkMappingConfig($extract_config, $file);
                        if (!$is_mapping_config) {
                            // Kiểm tra config phụ
                            $slave_configs = $extract_config->slave_extract_order_configs;
                            foreach ($slave_configs as $slave_config) {
                                $is_mapping_config = $this->checkMappingConfig($slave_config, $file);
                                if ($is_mapping_config) {
                                    $mapping_config = $slave_config;
                                    Log::info("File: " .$file_name . ", thuộc config: id=" . $mapping_config->id . ", name=" . $mapping_config->name);
                                    // Khởi tạo lại các thông số của config mới
                                    $this->reconstructDataConfig($mapping_config->id);
                                    break;
                                }
                            }
                            if (!$is_mapping_config) {
                                Log::error("Không có cấu hình nào phù hợp trong nhóm: " . $file_name);
                                $this->errors[] = "File chưa được hỗ trợ cấu hình: " . $file_name;
                                $csv_data[$file_name] = $this->getCsvDataFromPdf($file);
                            }
                        } else {
                            $mapping_config = $extract_config;
                            Log::info("File: " .$file_name . ", thuộc config: id=" . $mapping_config->id . ", name=" . $mapping_config->name);
                        }
                    } else {
                        $mapping_config = $extract_config;
                    }
                    if ($mapping_config) {
                        $convert_file_type = $mapping_config->convert_file_type;
                        $extract_data_config = $mapping_config->extract_data_config;
                        $convert_table_config =  $mapping_config->convert_table_config;
                        $restructure_data_config = $mapping_config->restructure_data_config;
                        $header_extractor_config = $mapping_config->extract_header_config;
                        $header_convert_table_config =  $mapping_config->convert_table_header_config;
                        $header_restructure_config = $mapping_config->restructure_header_config;
                    }
                } else {
                    $this->errors[] = "File chưa được hỗ trợ cấu hình: " . $file_name;
                    Log::error("Không tìm thấy cấu hình đã chọn, config_id=" . $config_id);
                    $csv_data[$file_name] = $this->getCsvDataFromPdf($file);
                }

                // Raw data
                $raw_data = $this->extractDataForDirect($file, $extract_data_config);

                $order_data = null;
                $order_header = null;
                // Table data & order data
                $extract_method = $extract_data_config->method;
                if ($convert_file_type == 'pdf') {
                    switch ($extract_method) {
                        case ExtractMethod::CAMELOT:
                            $raw_header = $this->extractHeaderForDirect($file, $header_extractor_config);
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
                            break;
                        case ExtractMethod::AI:
                            // Xử lý chỉ 1 PO
                            // $raw_data_table = $raw_data['line_items'];
                            // $raw_header_table = array();
                            // $raw_header_table[] = $raw_data['Info_PO'];
                            // $decimal_separator = isset($raw_data['Info_PO']['decimal']) ? $raw_data['Info_PO']['decimal'] : null;
                            // $order_data = $this->restructureDataByAi($raw_data_table, $restructure_data_config, $decimal_separator);
                            // $restruct_header = $this->restructureHeaderByAi($raw_header_table, $restructure_data_config);
                            // $order_header = $this->addCustomInfo($restruct_header[0]);
                            // $array_data = [
                            //     'headers' => $order_header,
                            //     'items' => $order_data,
                            // ];
                            // array_push($final_data, $array_data);

                            // Xử lý nhiều PO
                            foreach ($raw_data as $raw_data_po) {
                                $table_data_content = [];
                                if (isset($raw_data_po['content'])) {
                                    $table_data_content = $raw_data_po['content'];

                                    $raw_data_table = $table_data_content['line_items'];
                                    $raw_header_table = array();
                                    $raw_header_table[] = $table_data_content['Info_PO'];
                                    $decimal_separator = isset($table_data_content['Info_PO']['decimal']) ? $table_data_content['Info_PO']['decimal'] : null;
                                    $order_data = $this->restructureDataByAi($raw_data_table, $restructure_data_config, $decimal_separator);
                                    $restruct_header = $this->restructureHeaderByAi($raw_header_table, $restructure_data_config);
                                    $order_header = $this->addCustomInfo($restruct_header[0]);
                                    $array_data = [
                                        'headers' => $order_header,
                                        'items' => $order_data,
                                    ];
                                    array_push($final_data, $array_data);
                                }
                            }
                            break;
                    }

                } else if ($convert_file_type == 'excel') {
                    $table_area_info = json_decode($extract_data_config->table_area_info);
                    $header_item_type = isset($table_area_info->header_item_type) ? $table_area_info->header_item_type : '';
                    $header_key_names = isset($table_area_info->header_key_names) ? $table_area_info->header_key_names : [];

                    switch ($header_item_type) {
                        case 'header-item':
                            $order_data = $this->restructureDataDirect($raw_data, $restructure_data_config);

                            $array_data = $this->restructureHeaderItem($order_data, $header_key_names);
                            $final_data = array_merge($final_data, $array_data);
                            break;
                        case 'header-items':
                            $order_data = $this->restructureDataDirect($raw_data, $restructure_data_config);

                            $split_header_key = isset($table_area_info->split_header_key) ? $table_area_info->split_header_key : '';
                            $array_data = $this->restructureHeaderItems($order_data, $header_key_names, $split_header_key);
                            $final_data = array_merge($final_data, $array_data);
                            break;
                        case 'item-headers':
                            $start_index_header = isset($table_area_info->start_index_header) ? $table_area_info->start_index_header : 0;
                            $array_data = $this->restructureItemHeaders($raw_data, $restructure_data_config, $header_key_names, $start_index_header);
                            $final_data = array_merge($final_data, $array_data);
                            break;

                        default:
                            $raw_header = $this->extractHeaderForDirect($file, $header_extractor_config);
                            // Data
                            $order_data = $this->restructureDataDirect($raw_data, $restructure_data_config);
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
                // Kiểm tra items của phần tử cuối $final_data
                if (count($final_data) > 0) {
                    $last_item = end($final_data);
                    if (count($last_item['items']) == 0) {
                        Log::error("Convert file với data rỗng: " . $file_name);
                        $this->errors[] = "File chưa được hỗ trợ cấu hình: " . $file_name;
                        $csv_data[$file_name] = $this->getCsvDataFromPdf($file);
                    }
                } else {
                    Log::error("Loại file chưa được hỗ trợ: " . $file_name);
                    $this->errors[] = "File chưa được hỗ trợ cấu hình: " . $file_name;
                }
            } catch (\Throwable $exception) {
                Log::error($exception->getMessage());
                $this->errors[] = "File chưa được hỗ trợ cấu hình: " . $file_name;
                $csv_data[$file_name] = $this->getCsvDataFromPdf($file);
            }
            // Nếu có bất kỳ file nào convert lỗi thì gửi thông báo và không trả về data
            if ($csv_data) {
                $this->errors["csv_data"] = $csv_data;
                $final_data = [];
            }
            if (count($final_data) > 0) {
                // Sinh ra convert UID để xác đinh các PO chuyển đổi
                $convert_po_uid = uniqid('', true);
                foreach ($final_data as $index => $value) {
                    $final_data[$index]['convert_po_uid'] = $convert_po_uid;
                }
                // Lưu thông tin PO và file
                $file_service = $this->file_service;
                $file_path = $file_service->savePoFile($file);
                SaveRawPoData::dispatch($final_data, $file_path, $file_name, $convert_po_uid);
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
        $is_extract_by_ai = false;
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
        } else if ($this->data_extractor instanceof AiExtractorService) {
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
            $is_extract_by_ai = true;
        }
        $tables = $this->data_extractor->extract($file_path, $options);
        $choosen_tables = [];
        if ($is_extract_by_ai) {
            $choosen_tables = $tables;
        } else {
            if ($specify_table_number > 0) {
                $choosen_tables[] = $tables[$specify_table_number - 1];
            } else {
                for ($i = $exclude_head_tables_count; $i < count($tables) - $exclude_tail_tables_count; $i++) {
                    $choosen_tables[] = $tables[$i];
                }
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
                if (isset($restructure_data_config->decimal_separator) &&
                    $restructure_data_config->decimal_separator) {
                    $options['decimal_separator'] = $restructure_data_config->decimal_separator;
                }
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
            $remove_chars = ['.',',',' '];
            $customer_key = strtolower(str_replace($remove_chars, '', $customer_key));

            // Lồng các hàm REPLACE để loại bỏ các ký tự không mong muốn
            $replace_expression = 'name';
            foreach ($remove_chars as $char) {
                $replace_expression = "REPLACE($replace_expression, '$char', '')";
            }
            $query_expression = 'LOWER('. $replace_expression. ') = ?';
            // Kiểm tra bằng chuỗi customer_key
            $query = CustomerPartner::query()->where('customer_group_id', $this->customer_group_id)
                ->whereRaw($query_expression, [$customer_key]);
            $customer_partner = $query->first();

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
        if (array_key_exists('SapSoNumber', $table_data)) {
            // Trường hợp có cấu hình SapSoNumber
            $sap_so_number_info = $table_data['SapSoNumber'];
            if (isset($sap_so_number_info['join_after_add_customer']) && $sap_so_number_info['join_after_add_customer'] == true) {
                if (isset($sap_so_number_info['key_array'])) {
                    $key_array = $sap_so_number_info['key_array'];
                    $separator = $sap_so_number_info['separator'];
                    $value_array = [];
                    foreach ($key_array as $key) {
                        $value_array[] = $table_data[$key];
                    }
                    $filtered_arr = array_filter($value_array, function($value) {
                        return !is_null($value) && $value !== '';
                    });
                    $table_data['SapSoNumber'] = implode($separator, $filtered_arr);
                }
            }
        } else {
            // Thêm trường SapSoNumber mặc định nếu không có cấu hình
            if (array_key_exists('PoNumber', $table_data) && array_key_exists('PoDeliveryDate', $table_data)) {
                $po_delivery_date = str_replace('-', '', $table_data['PoDeliveryDate']);
                $table_data['SapSoNumber'] = $po_delivery_date ? $table_data['PoNumber'] . '-' . $po_delivery_date
                    : $table_data['PoNumber'];
            }
        }
        // Thêm trường SoSapNote (nếu có) sau khi có đầy đủ thông tin
        if (array_key_exists('SoSapNote', $table_data)) {
            $so_sap_note_info = $table_data['SoSapNote'];
            if (isset($so_sap_note_info['join_after_add_customer']) && $so_sap_note_info['join_after_add_customer'] == true) {
                if (isset($so_sap_note_info['key_array'])) {
                    $key_array = $so_sap_note_info['key_array'];
                    $separator = $so_sap_note_info['separator'];
                    $value_array = [];
                    foreach ($key_array as $key) {
                        $value_array[] = $table_data[$key];
                    }
                    $filtered_arr = array_filter($value_array, function($value) {
                        return !is_null($value) && $value !== '';
                    });
                    $table_data['SoSapNote'] = implode($separator, $filtered_arr);
                }
            }
        }
        return $table_data;
    }
    private function addCustomInfoToItem($data)
    {
        // Xử lý tìm tên sản phẩm theo mã SAP
        // Xử lý tăng tốc độ khi truy vấn bảng SAP
        // Step 1: Extract all SkuSapCode values
        $data_all_items = array_column($data, 'items');
        $data_items = array_column($data_all_items, '0');
        $sku_sap_codes = array_column($data_items, 'SkuSapCode');
        $sku_sap_names = array_column($data_items, 'SkuSapName');
        // Convert sku_sap_codes thành mảng không key
        $sku_sap_codes = array_values(array_filter($sku_sap_codes));
        // Check có yêu cầu lấy tên sản phẩm
        if ($sku_sap_names && $sku_sap_codes) {
            // Step 2: Query SapMaterial table once
            $chunk_size = 1000; // Kích thước mảng con
            $sku_sap_records = collect();
            foreach (array_chunk($sku_sap_codes, $chunk_size) as $chunk) {
                $partialResults = SapMaterial::whereIn('sap_code', $chunk)
                    ->select('sap_code', 'name')
                    ->get()
                    ->keyBy('sap_code');
                $sku_sap_records = $sku_sap_records->merge($partialResults);
            }
            // Step 3: Map SkuSapCode to SkuSapName
            $sku_sap_map = $sku_sap_records->mapWithKeys(function ($item) {
                return [$item->sap_code => $item->name];
            });

            // Step 4: Update $items with SkuSapName
            foreach ($data as $index => $item_data) {
                if (isset($item_data['items']) && isset($item_data['items'][0])) {
                    if (array_key_exists('SkuSapName', $item_data['items'][0]) &&
                        array_key_exists('SkuSapCode', $item_data['items'][0])) {
                        $sku_sap_code = $item_data['items'][0]['SkuSapCode'];
                        $sku_sap_name = $sku_sap_map->get($sku_sap_code, '');
                        $data[$index]['items'][0]['SkuSapName'] = $sku_sap_name;
                    }
                }
            }
        }
        return $data;
    }
    // Xử lý mẫu 1 header có 1 item
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
        $result = $this->addCustomInfoToItem($result);
        return $result;
    }
    // Xử lý mẫu 1 header có nhiều item
    private function restructureHeaderItems($order_data, $header_key_names, $split_header_key) {
        $result = array();
        $header = array();
        $items = array();
        $array_data = array();
        $row_count = count($order_data);
        foreach ($order_data as $index=>$data) {
            // Lưu data nếu gặp dòng header tiếp theo
            if ($data[$split_header_key]) {
                // Lưu lại data theo header trước đó
                if ($items) {
                    $array_data = [
                        'headers' => $header,
                        'items' => $items,
                    ];
                    array_push($result, $array_data);
                    $items = [];
                }
                $header = [];
                foreach ($header_key_names as $header_key) {
                    $header[$header_key] = $data[$header_key];
                }
                $header = $this->addCustomInfo($header);
                continue;
            } else {
                $items[] = array_diff_key($data, array_flip($header_key_names));
                // Xử lý data cuối
                if ($index == ($row_count - 1)) {
                    if ($items) {
                        $array_data = [
                            'headers' => $header,
                            'items' => $items,
                        ];
                        array_push($result, $array_data);
                        $items = [];
                    }
                }
            }
        }
        $result = $this->addCustomInfoToItem($result);
        return $result;
    }
    // Xử lý mẫu 1 item có nhiều header
    private function restructureItemHeaders($raw_data, $restructure_data_config = null, $header_key_names, $start_index_header) {
        $result = array();
        $convert_data = array();

        // Tìm các index cột có header hợp lệ
        $header_row = $raw_data[0];
        $header_indexes = array_keys($header_row, !null);
        $header_indexes = array_filter($header_indexes, function($index) use ($start_index_header) {
            return $index >= $start_index_header;
        });
        // Duyệt theo từng key header
        foreach ($header_indexes as $header_index) {
            // Duyệt theo từng data theo số lượng
            for ($index = 1; $index < count($raw_data); $index++) {
                if ($raw_data[$index][$header_index] > 0) {
                    $items = [];
                    // Lấy data item
                    $items = array_slice($raw_data[$index], 0, $start_index_header);
                    // Đưa số lượng vào item
                    array_push($items, $raw_data[$index][$header_index]);
                    // Đưa key của header vào item
                    array_push($items, $header_row[$header_index]);
                    // Lưu data hoàn chỉnh
                    array_push($convert_data, $items);
                }
            }
        }
        // Xử lý format data đã convert
        $restruct_data = $this->restructureDataDirect($convert_data, $restructure_data_config);
        $result = $this->restructureHeaderItem($restruct_data, $header_key_names);

        return $result;
    }

    public function extractDataForConfig()
    {
        $raw_data = null;
        $file = $this->request->file('file');
        $file_service = $this->file_service;
        $file_path = $file_service->saveTemporaryFile($file);

        try {
            $raw_data = $this->extractData($file_path);
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        } finally {
            $file_service->deleteTemporaryFile($file_path);
        }

        return $raw_data;
    }

    public function extractDataForDirect($file, $extract_data_config)
    {
        $file_service = $this->file_service;
        $file_path = $file_service->saveTemporaryFile($file);
        $raw_data = null;

        try {
            $raw_data = $this->extractData($file_path, $extract_data_config);
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        } finally {
            $file_service->deleteTemporaryFile($file_path);
        }

        return $raw_data;
    }

    public function extractHeaderForDirect($file, $extract_header_config)
    {
        $file_service = $this->file_service;
        $file_path = $file_service->saveTemporaryFile($file);
        $raw_data = null;

        try {
            $raw_data = $this->extractHeader($file_path, $extract_header_config);
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        } finally {
            $file_service->deleteTemporaryFile($file_path);
        }

        return $raw_data;
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
            $result = array();
            if ($this->request->convert_file_type == 'excel') {
                $table_area_info = json_decode($this->request->table_area_info);
                $header_item_type = isset($table_area_info->header_item_type) ? $table_area_info->header_item_type : '';
                $header_key_names = isset($table_area_info->header_key_names) ? $table_area_info->header_key_names : [];

                switch ($header_item_type) {
                    case 'header-item':
                        $restruct_data = $this->restructureData($table_data);
                        $result = $this->restructureHeaderItem($restruct_data, $header_key_names);
                        break;
                    case 'header-items':
                        $restruct_data = $this->restructureData($table_data);
                        $split_header_key = isset($table_area_info->split_header_key) ? $table_area_info->split_header_key : '';
                        $result = $this->restructureHeaderItems($restruct_data, $header_key_names, $split_header_key);
                        break;
                    case 'item-headers':
                        $start_index_header = isset($table_area_info->start_index_header) ? $table_area_info->start_index_header : 0;
                        $result = $this->restructureItemHeaders($table_data, null, $header_key_names, $start_index_header);
                        break;

                    default:
                        $restruct_data = $this->restructureData($table_data);
                        $result =  $this->addCustomInfo($restruct_data);
                        break;
                }
            } else {
                $restruct_data = $this->restructureData($table_data);
                $result =  $this->addCustomInfo($restruct_data);
            }
            return $result;
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

    public function restructureDataByAi($table_data, $restructure_config, $decimal_separator = null)
    {
        try {
            $new_restructure_config = clone $restructure_config;
            $structure = json_decode($new_restructure_config->structure, true);
            $data_config = json_encode($structure['data_config']);
            $new_restructure_config->structure = $data_config;
            $new_restructure_config->decimal_separator = $decimal_separator;
            return $this->restructureData($table_data, $new_restructure_config);
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function restructureHeaderByAi($table_data, $restructure_config)
    {
        try {
            $new_restructure_config = clone $restructure_config;
            $structure = json_decode($new_restructure_config->structure, true);
            $header_config = json_encode($structure['header_config']);
            $new_restructure_config->structure = $header_config;
            return $this->restructureData($table_data, $new_restructure_config);
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
    // Hàm kiểm tra config có mapping với file không
    private function checkMappingConfig($extract_order_config, $file)
    {
        $result = false;
        $file_service = $this->file_service;
        $file_path = $file_service->saveTemporaryFile($file);

        try {
            $extract_order_config->load(['extract_data_config']);
            $extract_data_config = $extract_order_config->extract_data_config;
            $advanced_settings_info = json_decode($extract_data_config->advanced_settings_info);
            $check_mapping_config = $advanced_settings_info->check_mapping_config ?? null;

            if ($check_mapping_config) {
                $check_string_key = $check_mapping_config->check_string_key ?? null;

                if ($check_string_key) {
                    // Kiểm tra mẫu theo chuỗi từ khóa
                    $string_key = $check_string_key->string_key;
                    $page = $check_string_key->page ?? 1;
                    $text_locator = new PdfTextLocatorService();
                    $get_full_text_result = $text_locator->getFullText($file_path, $page);

                    if (!isset($get_full_text_result['error']) && strpos($get_full_text_result[0], $string_key) !== false) {
                        // Kiểm tra thêm vị trí các chuỗi text
                        $check_text_break_line = $check_string_key->check_text_break_line ?? null;

                        if ($check_text_break_line) {
                            $text_break_line_info = array_map(function($text_break_line) use ($file_path, $text_locator) {
                                $text1 = $text_break_line->text_1;
                                $text2 = $text_break_line->text_2;

                                $text1_position = $text_locator->findTextPosition($file_path, $text1->page, $text1->text, $text1->index);
                                $text2_position = $text_locator->findTextPosition($file_path, $text2->page, $text2->text, $text2->index);

                                if (isset($text1_position['error']) || isset($text2_position['error'])) {
                                    return false;
                                }

                                return $text1_position['rect_coord']['x0'] >= $text2_position['rect_coord']['x0'];
                            }, $check_text_break_line);

                            // Nếu tất cả phần tử $text_break_line_info là true thì $result là true
                            if (!in_array(false, $text_break_line_info, true)) {
                                $result = true;
                            }
                        } else {
                            $result = true;
                        }
                    }
                } else {
                    // Kiểm tra mẫu theo vùng tọa độ
                    $check_table_areas = $check_mapping_config->check_table_areas;
                    $x_coordinates = $check_mapping_config->columns->x_coordinates ?? [];
                    $check_condition = $check_mapping_config->check_condition;
                    $data_extractor = new CamelotExtractorService();

                    $value_table_area = $data_extractor->getValueTableAreas($file_path, $check_table_areas, $x_coordinates);
                    $table_0 = $value_table_area[0] ?? null;

                    if ($table_0) {
                        $csv = Reader::createFromString($table_0);
                        $records = $csv->getRecords();
                        $table_0_array = iterator_to_array($records);
                        $check_value = preg_replace('/\s+/', '', $table_0_array[0][0] ?? '');

                        // Check chuỗi $value_table_area[0] có chứa chuỗi $check_condition
                        if (strpos($check_value, $check_condition) !== false) {
                            $result = true;
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            $result = false;
        } finally {
            $file_service->deleteTemporaryFile($file_path);
        }

        return $result;
    }

    public function getConvertConfigList()
    {
        $query = ExtractOrderConfig::where('is_official', true)->orderBy('customer_group_id')->orderBy('order');
        $query->with([
            'customer_group', 'master_extract_order_config'
        ]);
        $convert_config_list = $query->get();
        return $convert_config_list;
    }

    public function updateConvertConfig($config_id)
    {
        try {
            $validator = Validator::make($this->data, [
                'customer_group_id' => 'required',
                'name' => 'required',
                'convert_file_type' => 'required',
                'active' => 'required',
            ], [
                'customer_group_id.required' => 'Nhóm khách hàng là bắt buộc',
                'name.required' => 'Tên cấu hình là bắt buộc',
                'convert_file_type.required' => 'Loại file convert là bắt buộc',
                'active.required' => 'Trạng thái là bắt buộc',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
                $result['errors'] = $this->errors;
                return $result;
            } else {
                DB::beginTransaction();
                $extract_order_config = ExtractOrderConfig::find($config_id);
                if (!$extract_order_config) {
                    $this->message = 'Config không tồn tại';
                    return;
                }
                $extract_order_config->customer_group_id = $this->data['customer_group_id'];
                $extract_order_config->name = $this->data['name'];
                $extract_order_config->convert_file_type = $this->data['convert_file_type'];
                $extract_order_config->order = $this->data['order'];
                $extract_order_config->is_config_group = $this->data['is_config_group'] == "true" ? true: false;
                $extract_order_config->is_master_config_group = $this->data['is_master_config_group'] == "true" ? true: false;
                $extract_order_config->is_slave_config_group = $this->data['is_slave_config_group'] == "true" ? true: false;
                $extract_order_config->master_config_group_id = $this->data['master_config_group_id'];
                $extract_order_config->active = $this->data['active'] == "true" ? true: false;

                $extract_order_config->save();

                DB::commit();
                return $extract_order_config;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteConvertConfig($id)
    {
        try {
            $extract_order_config = ExtractOrderConfig::findOrFail($id);
            $extract_order_config->delete();
            return $extract_order_config;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function reconstructDataConfig($config_id)
    {
        $extract_config = ExtractOrderConfig::find($config_id);
        if ($extract_config) {
            $convert_file_type = $extract_config->convert_file_type;
            // Data
            $table_convert_config =  $extract_config->convert_table_config;
            $data_restruct_config = $extract_config->restructure_data_config;
            // Header
            $extract_header_config = $extract_config->extract_header_config;
            $table_convert_header_config =  $extract_config->convert_table_header_config;
            $restruct_header_config = $extract_config->restructure_header_config;
            // Customer group
            $customer_group_id = $extract_config->customer_group_id;
        }
        // Data
        $data_extractor = null;
        $header_extractor = null;
        switch ($convert_file_type) {
            case 'pdf':
                $data_extractor = new CamelotExtractorService();
                $header_extractor = new CamelotExtractorService();
                break;
            case 'excel':
                $data_extractor = new ExcelExtractorService();
                $header_extractor = new ExcelExtractorService();
                break;
            default:
                $data_extractor = new CamelotExtractorService();
                $header_extractor = new CamelotExtractorService();
                break;
        }
        $table_converter = new RegexMatchConverter();
        $data_restructure = new IndexArrayMappingRestructure();
        // Header
        $header_table_converter = new LeagueCsvConverter();
        $header_restructure = new MergeIndexArrayMappingRestructure();

        // Data config
        $method = $table_convert_config->method; // Có thể là regex, leaguecsv
        switch ($method) {
            case ConvertMethod::REGEXMATCH:
                $table_converter = new RegexMatchConverter();
                break;
            case ConvertMethod::REGEXSPLIT:
                $table_converter = new RegexSplitConverter();
                break;
            case ConvertMethod::LEAGUECSV:
                $table_converter = new LeagueCsvConverter();
                break;
            case ConvertMethod::MANUAL:
                $table_converter = new ManualConverter();
                break;
            default:
                $table_converter = new RegexMatchConverter();
        }

        $method = $data_restruct_config->method; // Có thể là regex, leaguecsv
        switch ($method) {
            case RestructureMethod::INDEXARRAYMAPPING:
                $data_restructure = new IndexArrayMappingRestructure();
                break;
            case RestructureMethod::KEYARRAYMAPPING:
                $data_restructure = new KeyArrayMappingRestructure();
                break;
            case RestructureMethod::MERGEINDEXARRAYMAPPING:
                $data_restructure = new MergeIndexArrayMappingRestructure();
                break;
            case RestructureMethod::SEARCHTEXTARRAYMAPPING:
                $data_restructure = new SearchTextArrayMappingRestructure();
                break;
            default:
                $data_restructure = new IndexArrayMappingRestructure();
        }

        // Header config
        $method = $table_convert_header_config->method;
        switch ($method) {
            case ConvertMethod::REGEXMATCH:
                $header_table_converter = new RegexMatchConverter();
                break;
            case ConvertMethod::REGEXSPLIT:
                $header_table_converter = new RegexSplitConverter();
                break;
            case ConvertMethod::LEAGUECSV:
                $header_table_converter = new LeagueCsvConverter();
                break;
            case ConvertMethod::MANUAL:
                $header_table_converter = new ManualConverter();
                break;
            default:
                $header_table_converter = new RegexMatchConverter();
        }
        $method = $restruct_header_config->method;
        switch ($method) {
            case RestructureMethod::INDEXARRAYMAPPING:
                $header_restructure = new IndexArrayMappingRestructure();
                break;
            case RestructureMethod::KEYARRAYMAPPING:
                $header_restructure = new KeyArrayMappingRestructure();
                break;
            case RestructureMethod::MERGEINDEXARRAYMAPPING:
                $header_restructure = new MergeIndexArrayMappingRestructure();
                break;
            case RestructureMethod::SEARCHTEXTARRAYMAPPING:
                $header_restructure = new SearchTextArrayMappingRestructure();
                break;
            default:
                $header_restructure = new MergeIndexArrayMappingRestructure();
        }

        // Data
        $this->data_extractor = $data_extractor;
        $this->table_converter = $table_converter;
        $this->data_restructure = $data_restructure;
        // Header
        $this->header_extractor = $header_extractor;
        $this->header_table_converter = $header_table_converter;
        $this->header_restructure = $header_restructure;
    }

    // Hàm lấy data csv từ pdf
    public function getCsvDataFromPdf($file)
    {
        $result = [];
        $file_service = $this->file_service;
        $file_path = $file_service->saveTemporaryFile($file);

        try {
            $data_extractor = new CamelotExtractorService();
            $result = $data_extractor->getFileContent($file_path);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
        } finally {
            $file_service->deleteTemporaryFile($file_path);
        }

        return $result;
    }

    public function saveRawPoData($final_data, $po_file_path, $po_file_name, $convert_po_uid) {
        try {
            DB::beginTransaction();
            $user_id = $this->current_user->id;
            $po_upload_file = PoUploadFile::create([
                'file_name' => $po_file_name,
                'path' => $po_file_path,
                'convert_po_uid' => $convert_po_uid,
                'created_by' => $user_id,
            ]);

            // Group dữ liệu theo po_number
            $grouped_data = [];
            foreach ($final_data as $data) {
                $po_number = '';
                $po_number = $data['headers']['PoNumber'];
                if (!isset($grouped_data[$po_number])) {
                    $grouped_data[$po_number] = [
                        'headers' => $data['headers'],
                        'items' => []
                    ];
                }
                $grouped_data[$po_number]['items'] = array_merge($grouped_data[$po_number]['items'], $data['items']);
            }

            // Lưu các header và item dữ liệu
            foreach ($grouped_data as $po_number => $data) {
                // Lưu các header vào bảng
                $po_headers = $data['headers'];
                $raw_po_header = RawPoHeader::create([
                    'po_number' => $po_headers['PoNumber'],
                    'po_delivery_date' => isset($po_headers['PoDeliveryDate']) ? $po_headers['PoDeliveryDate'] : NULL,
                    'customer_name' => isset($po_headers['CustomerKey']) ? $po_headers['CustomerKey'] : NULL,
                    'customer_code' => isset($po_headers['CustomerCode']) ? $po_headers['CustomerCode'] : NULL,
                    'note' => isset($po_headers['CustomerNote']) ? $po_headers['CustomerNote'] : NULL,
                    'level2' => isset($po_headers['CustomerLevel2']) ? $po_headers['CustomerLevel2'] : NULL,
                    'level3' => isset($po_headers['CustomerLevel3']) ? $po_headers['CustomerLevel3'] : NULL,
                    'level4' => isset($po_headers['CustomerLevel4']) ? $po_headers['CustomerLevel4'] : NULL,
                    'sap_so_number' => isset($po_headers['SapSoNumber']) ? $po_headers['SapSoNumber'] : NULL,
                    'convert_po_uid' => $convert_po_uid,
                    'created_by' => $user_id,
                ]);

                $raw_po_header_id = $raw_po_header->id;

                // Lưu các item dữ liệu vào bảng
                $po_data_items = $data['items'];
                foreach ($po_data_items as $po_data_item) {
                    RawPoDataItem::create([
                        'raw_po_header_id' => $raw_po_header_id,
                        'customer_sku_unit' => isset($po_data_item['OrdUnit']) ? $po_data_item['OrdUnit'] : NULL,
                        'customer_sku_code' => isset($po_data_item['ProductID']) ? $po_data_item['ProductID'] : NULL,
                        'quantity1_po' => isset($po_data_item['Quantity1']) && is_numeric($po_data_item['Quantity1']) ? $po_data_item['Quantity1'] : NULL,
                        'quantity2_po' => isset($po_data_item['Quantity2']) && is_numeric($po_data_item['Quantity2']) ? $po_data_item['Quantity2'] : NULL,
                        'customer_sku_name' => isset($po_data_item['ProductName']) ? $po_data_item['ProductName'] : NULL,
                        'price_po' => isset($po_data_item['ProductPrice']) ? $po_data_item['ProductPrice'] : NULL,
                        'price_po' => isset($po_data_item['ProductPrice']) && is_numeric($po_data_item['ProductPrice']) ? $po_data_item['ProductPrice'] : NULL,
                        'amount_po' => isset($po_data_item['ProductAmount']) && is_numeric($po_data_item['ProductAmount']) ? $po_data_item['ProductAmount'] : NULL,
                    ]);
                }
            }
            DB::commit();
            return true;

        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            DB::rollBack();
            return false;
        }
    }
}
