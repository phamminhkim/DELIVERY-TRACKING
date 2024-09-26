<?php

namespace App\Repositories\Business;

use App\Models\Business\UploadedFile;
use App\Models\Master\CustomerMaterial;
use App\Models\Master\SapMaterial;
use App\Models\Master\SapCompliance;
use App\Models\Master\CustomerGroup;
use App\Models\Master\CustomerPartner;
use App\Models\Master\MaterialCombo;
use App\Models\Master\MaterialDonated;
use App\Models\Master\MaterialCategoryType;
use App\Models\Master\SapMaterialMapping;
use App\Models\Master\SapUnit;
use App\Models\Master\Warehouse;
use App\Services\Excel\ExcelExtractor;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Implementations\Files\LocalFileService;
use App\Services\Sap\SapApiHelper;
use Illuminate\Support\Facades\Log;
use Throwable;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class CheckDataRepository extends RepositoryAbs
{

    public function checkMaterialSAP()
    {
        try {
            $validator = Validator::make($this->data, [
                'customer_group_id' => 'required',
                'items' => 'required|array',
                'items.*.customer_sku_code' => 'nullable',
                'items.*.customer_sku_unit' => 'nullable',
                'items.*.sap_so_number' => 'required',
                'items.*.promotion' => 'nullable',
                'items.*.quantity2_po' => 'required|numeric',
            ], [
                'items.array' => 'items phải là mảng',
                'items.required' => 'Data là bắt buộc',
                'customer_group_id.required' => 'Nhóm khách hàng là bắt buộc',
                'sap_so_number.required' => 'Sap so number là bắt buộc',
                'quantity2_po.required' => 'Số lượng po là bắt buộc',
            ]);

            if ($validator->fails()) {
                $this->message = $validator->errors()->first();
                return false;
            }

            $customer_group_id = $this->data['customer_group_id'];
            $items = $this->data['items'];

            // Lấy thông tin nhóm khách hàng từ bảng customer_materials
            $customerMaterials = CustomerMaterial::where('customer_group_id', $customer_group_id)->get();

            if (!$customerMaterials) {
                $this->message = 'Không tìm thấy nhóm khách hàng';
                return false;
            }

            $mappingData = [];
            $existingCodes = [];
            $existingSapSoNumber = [];
            // $foundMapping = false;

            // Tiếp tục xử lý với mảng $items chứa dữ liệu nhập vào
            foreach ($items as $item) {
                $foundMapping = false;
                if (!empty($item['customer_sku_code'])) {
                    // Tiếp tục xử lý thông tin khi 'customer_sku_code' không trống
                    $customer_sku_code = $item['customer_sku_code'];
                    $sap_so_number = $item['sap_so_number'];
                    $checkCode = $customer_sku_code . $sap_so_number;
                    if (in_array($checkCode, $existingCodes)) {
                        // continue;
                    }
                    $existingCodes[] = $checkCode;
                    // Kiểm tra sự tồn tại của trường 'customer_sku_unit'
                    if (isset($item['customer_sku_unit'])) {
                        $customer_sku_unit = $item['customer_sku_unit'];
                    } else {
                        $customer_sku_unit = null; // Xử lý khi trường không tồn tại
                    }
                    // Kiểm tra sự tồn tại của trường 'sap_so_number'
                    if (isset($item['sap_so_number'])) {
                        $sap_so_number = $item['sap_so_number'];
                    } else {
                        $sap_so_number = null; // Xử lý khi trường không tồn tại
                    }
                    // Kiểm tra sự tồn tại của trường 'compliance'
                    if (isset($item['promotion'])) {
                        $promotion = $item['promotion'];
                    } else {
                        $promotion = null; // Xử lý khi trường không tồn tại
                    }
                } else {
                    continue;
                }
                // Kiểm tra xem có sự ánh xạ trực tiếp trong bảng SapMaterial hay không
                $sapMaterial = SapMaterial::where('bar_code', $customer_sku_code)
                    ->where('is_deleted', 0)
                    ->orderByRaw('CASE WHEN priority IS NOT NULL THEN 0 ELSE 1 END, priority ASC')
                    ->orderBy('id', 'asc') // Sắp xếp theo id nếu bar_code không có dữ liệu trong cột priority
                    ->first();

                if ($sapMaterial && $sapMaterial->is_deleted != 1) {
                    // Thêm thông tin vào mappingData
                    $sap_code = $sapMaterial->sap_code;
                    $bar_code = $sapMaterial->bar_code;
                    $unit_code = $sapMaterial->unit_code;
                    $name = $sapMaterial->name;
                    $unit_id = $sapMaterial->unit_id;
                    $quantity2_po = $item['quantity2_po'];

                    $sapUnit = SapUnit::find($unit_id);
                    if ($sapUnit) {
                        $unit_code = $sapUnit->unit_code;
                    } else {
                        $unit_code = null; // Xử lý khi đơn vị không tồn tại
                    }
                    $foundMapping = false;

                    $temp_item['barcode'] = $bar_code;
                    $temp_item['sku_sap_code'] = $sap_code;
                    $temp_item['sku_sap_name'] = $name;
                    $temp_item['sku_sap_unit'] = $unit_code;
                    $temp_item['sku_sap_unit_id'] = $unit_id;
                    $temp_item['quantity3_sap'] = $quantity2_po;
                    $mappingData[] = $item;
                } else {

                    // Kiểm tra ánh xạ trong bảng SapMaterialMapping
                    $sapMaterialMappings = SapMaterialMapping::whereHas('customer_material', function ($query) use ($customer_group_id, $customer_sku_code) {
                        $query->where('customer_group_id', $customer_group_id)
                            ->where('customer_sku_code', $customer_sku_code);
                    })->get();

                    $temp_item = $item;
                    foreach ($sapMaterialMappings as $sapMaterialMapping) {
                        $sap_material_id = $sapMaterialMapping->sap_material_id;
                        $conversion_rate_sap = $sapMaterialMapping->conversion_rate_sap;
                        $customer_number = $sapMaterialMapping->customer_number;
                        $percentage = $sapMaterialMapping->percentage;

                        if ($customer_number != 0) {
                            $sapMaterial = SapMaterial::find($sap_material_id);

                            if ($sapMaterial) {
                                // Thêm thông tin vào mappingData
                                $sap_code = $sapMaterial->sap_code;
                                $bar_code = $sapMaterial->bar_code;
                                $unit_code = $sapMaterial->unit_code;
                                $name = $sapMaterial->name;
                                $unit_id = $sapMaterial->unit_id;

                                $sapUnit = SapUnit::find($unit_id);
                                if ($sapUnit) {
                                    $unit_code = $sapUnit->unit_code;
                                } else {
                                    $unit_code = null; // Xử lý khi đơn vị không tồn tại
                                }
                                $quantity2_po = $item['quantity2_po'];
                                $quantity3_sap = (($quantity2_po * $conversion_rate_sap) / $customer_number) * ($percentage / 100);

                                $temp_item['barcode'] = $bar_code;
                                $temp_item['sku_sap_code'] = $sap_code;
                                $temp_item['sku_sap_name'] = $name;
                                $temp_item['sku_sap_unit'] = $unit_code;
                                $temp_item['sku_sap_unit_id'] = $unit_id;
                                $temp_item['quantity3_sap'] = $quantity3_sap;
                                $mappingData[] = $temp_item;
                                $foundMapping = true;
                            }
                        }
                    }
                    if (!$foundMapping) {

                        // Cả hai bảng đều không có dữ liệu cho mã này, thêm một bản ánh xạ với các giá trị null và gắn quantity2_po cho quantity3_sap
                        $temp_item['barcode'] = null;
                        $temp_item['sku_sap_code'] = null;
                        $temp_item['sku_sap_name'] = null;
                        $temp_item['sku_sap_unit'] = null;
                        $temp_item['sku_sap_unit_id'] = null;
                        $temp_item['quantity3_sap'] = $item['quantity2_po'];
                        $mappingData[] = $item;
                    }
                }
            }
            return [
                'success' => true,
                'items' => $mappingData
            ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
    public function checkCustomer()
    {
        try {
            $validator = Validator::make($this->data, [
                'customer_group_id' => 'required',
                'items' => 'required|array',
                'items.*.customer_key' => 'required',
            ], [
                'items.array' => 'Data phải là mảng',
                'items.required' => 'Data là bắt buộc',
                'customer_key.required' => 'Customer key là bắt buộc',
            ]);

            if ($validator->fails()) {
                $this->message = $validator->errors()->first();
                return false;
            }
            $customer_group_id = $this->data['customer_group_id'];
            $items = $this->data['items'];

            $customer_data = [];
            // Xử lý lệnh so sánh customer key
            $remove_chars = ['.',',',' '];
            $replace_expression = 'name';
                foreach ($remove_chars as $char) {
                    $replace_expression = "REPLACE($replace_expression, '$char', '')";
                }
                $query_expression = 'LOWER('. $replace_expression. ') = ?';

            // Kiểm tra xem tất cả key không tồn tại
            $customer_keys = array_map(function($item) use ($remove_chars) {
                return strtolower(str_replace($remove_chars, '', trim($item['customer_key'])));
            }, $items);

            $existingPartners = CustomerPartner::where('customer_group_id', $customer_group_id)
                ->where(function($query) use ($query_expression, $customer_keys) {
                    foreach ($customer_keys as $key) {
                        $query->orWhereRaw($query_expression, [$key]);
                    }
                })
                ->first();
            if (!$existingPartners) {
                $this->errors = [
                    'customer_group_id' => $customer_group_id,
                    'items' => $items,
                    'message' => 'Tất cả Key khách hàng chưa có trong nhóm khách hàng này.
                        Cần kiểm tra và cập nhật Key trong bảng KH đối tác!',
                ];
                return false;
            }

            foreach ($items as $item) {
                $customer_key = $item['customer_key'];
                $cleared_customer_key = strtolower(str_replace($remove_chars, '', $customer_key));
                $query = CustomerPartner::query()->where('customer_group_id', $customer_group_id)
                    ->whereRaw($query_expression, [$cleared_customer_key]);
                $customer_partner = $query->first();

                $customer_code = null;
                $customer_note = null;
                $customer_LV2 = null;
                $customer_LV3 = null;
                $customer_LV4 = null;

                if ($customer_partner) {
                    $customer_code = $customer_partner->code;
                    $customer_note = $customer_partner->note;
                    $customer_LV2 = $customer_partner->LV2;
                    $customer_LV3 = $customer_partner->LV3;
                    $customer_LV4 = $customer_partner->LV4;
                }

                $customer_data[] = [
                    'customer_key' => $customer_key,
                    'customer_code' => $customer_code,
                    'customer_note' => $customer_note,
                    'customer_LV2' => $customer_LV2,
                    'customer_LV3' => $customer_LV3,
                    'customer_LV4' => $customer_LV4,
                ];
            }
            // Kiểm tra và lấy SoSapNote của KH nếu có
            $so_sap_note_syntax = $this->getSoSapNoteSyntax($customer_group_id);

            return [
                // 'success' => true,
                'customer_group_id' => $customer_group_id,
                'items' => $customer_data,
                'so_sap_note_syntax' => $so_sap_note_syntax
            ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
    public function checkCompliance()
    {
        try {
            $validator = Validator::make($this->data, [
                'items' => 'required|array',
                'items.*.sap_code' => 'required',
                'items.*.unit_code' => 'required',
                'items.*.quantity1_po' => 'required',
                'items.*.quantity2_po' => 'required',
            ], [
                'items.array' => 'Data phải là mảng',
                'items.required' => 'Data là bắt buộc',
                'sap_code.required' => 'Mã SP là bắt buộc',
                'unit_code.required' => 'Đơn vị tính là bắt buộc',
                'quantity1_po.required' => 'Qty là bắt buộc',
                'quantity2_po.required' => 'PO_qty là bắt buộc',
            ]);

            if ($validator->fails()) {
                $this->message = $validator->errors()->first();
                return false;
            }

            $check_compliance = [];

            foreach ($this->data['items'] as $item) {
                $sap_code = $item['sap_code'];
                $unit_code = $item['unit_code'];
                $quantity1_po = $item['quantity1_po'];
                $quantity2_po = $item['quantity2_po'];

                // Kiểm tra xem có đơn vị tính trong bảng SapCompliance hay không
                $sapCompliance = SapCompliance::where('sap_code', $sap_code)
                    ->whereHas('unit', function ($query) use ($unit_code) {
                        $query->where('unit_code', $unit_code);
                    })
                    ->first();

                if ($sapCompliance) {
                    $unit_code = $sapCompliance->unit->unit_code;
                    $compliance = $sapCompliance->compliance;

                    $itemData = [
                        'sap_code' => $sap_code,
                        'unit_code' => $unit_code,
                        'quantity1_po' => $quantity1_po,
                        'quantity2_po' => $quantity2_po,
                    ];

                    // Kiểm tra dữ liệu quy cách
                    if ($compliance !== null) {
                        $itemData['compliance'] = $compliance;

                        // Kiểm tra chia hết cho compliance
                        $isCompliant = ($compliance !== 0) && (($quantity1_po * $quantity2_po) % $compliance === 0);
                        $itemData['is_compliant'] = $isCompliant;
                    } else {
                        $itemData['compliance'] = null;
                        $itemData['is_compliant'] = true;
                    }

                    $check_compliance[] = $itemData;
                }
            }

            $result = [
                'success' => true,
                'items' => $check_compliance
            ];

            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
    public function checkPromotions()
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'customer_group_id' => 'required',
                'items' => 'required|array',
                'items.*.sap_code' => '',
                'items.*.bar_code' => '',
            ], [
                'items.array' => 'Data phải là mảng',
                'items.required' => 'Data là bắt buộc',
                'customer_group_id.required' => 'Nhóm khách hàng là bắt buộc',
                'sap_code.required' => 'Mã SP là bắt buộc',
                'bar_code.required' => 'Barcode là bắt buộc',
            ]);

            if ($validator->fails()) {
                $this->message = $validator->errors()->first();
                return false;
            }

            $customer_group_id = $this->request->input('customer_group_id');
            $items = $this->request->input('items');

            $customerMaterials = CustomerMaterial::where('customer_group_id', $customer_group_id)->get();

            if (!$customerMaterials) {
                $this->message = 'Không tìm thấy nhóm khách hàng';
                return false;
            }
            $mappingData = [];
            foreach ($items as $item) {
                $sap_code = isset($item['sap_code']) ? $item['sap_code'] : null;
                $bar_code = isset($item['bar_code']) ? $item['bar_code'] : null;
                $name = isset($item['name']) ? $item['name'] : null;
                $promotion_category = isset($item['promotion_category']) ? $item['promotion_category'] : null;

                $materialCombo = null;
                $extra_offer = ''; //
                $promotion_category = ''; //

                // Kiểm tra xem sap_code hoặc bar_code có được cung cấp hay không
                if (!empty($sap_code) || !empty($bar_code)) {
                    $materialCombo = MaterialCombo::where('customer_group_id', $customer_group_id)
                        ->when(!empty($sap_code), function ($query) use ($sap_code) {
                            return $query->where('sap_code', $sap_code);
                        })
                        ->when(!empty($bar_code), function ($query) use ($bar_code) {
                            return $query->where('bar_code', $bar_code);
                        })
                        ->first();
                }
                if ($materialCombo) {
                    // $combo_category_type = MaterialCategoryType::where('name', 'Combo')
                    //     ->where('is_deleted', false)
                    //     ->first();

                    // $promotion_category = $combo_category_type ? 'Combo' : null;
                    $promotion_category = 'X';
                    $name = $materialCombo->name;
                } else {
                    $sapMaterial = SapMaterial::where('sap_code', $sap_code)->first();

                    if ($sapMaterial) {
                        $name = $sapMaterial->name;

                        $materialDonated = MaterialDonated::where('sap_code', $sap_code)->first();

                        if ($materialDonated) {
                            // $donated_category_type = MaterialCategoryType::where('name', 'ExtraOffer')
                            //     ->where('is_deleted', false)
                            //     ->first();

                            // $promotion_category = $donated_category_type ? 'ExtraOffer' : null;
                            $extra_offer  = 'X';
                        }
                    }
                }
                // if ($promotion_category !== null) {
                // $item['promotion_category'] = $promotion_category;
                $item['name'] = $name;
                if ($promotion_category == 'X' || $extra_offer == 'X') {
                    $mappingData[] = [
                        'sap_code' => $sap_code,
                        'bar_code' => $bar_code,
                        'name' => $name,
                        'promotion_category' => $promotion_category,
                        'extra_offer' =>  $extra_offer,
                    ];
                }

                // }
            }
            return [
                'success' => true,
                'items' => $mappingData
            ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }

    public function checkInventory()
    {
        $result = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($this->request->all(), [
                'data' => 'required|array',
            ], [
                'data.array' => 'data phải là mảng',
                'data.required' => 'data là bắt buộc',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
            } else {
                $fields = $this->request->all();

                $sapData = [
                    "ID" => "1001",
                    "action_name" => "FETCH_MATERIAL_INVENTORY",
                    "BODY" => []
                ];
                $warehouse = null;
                foreach ($fields['data'] as $value) {
                    if ($warehouse == null) {
                        $warehouse_id = $value['warehouse_id'];
                        $warehouse = Warehouse::where('id',  $warehouse_id)->first();
                    }
                    $sapData['BODY'][] = [
                        "materials" => $value['materials'],
                        "warehouse_code" => $warehouse->code,
                    ];
                }
                $json = SapApiHelper::postData(json_encode($sapData));
                // dd($json);
                if (!$json['success']) {
                    $this->errors['sap_error'] = $json['error'];
                    return null;
                }
                $jsonString = json_encode($json); // Convert the array to a JSON string
                $jsonData = json_decode($jsonString, true);

                if (!empty($jsonData['data'])) {
                    // dd($jsonData['data']);

                    foreach ($jsonData['data'] as $json_value) {
                        $quantity = $json_value['ATP_QUANTITY'];
                        $result[] = [
                            "MATERIAL" => $json_value['MATERIAL'],
                            "ATP_QUANTITY" => $quantity,
                        ];
                    }
                }
            }
            DB::commit();
            return $result;
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function checkPrice()
    {
        $result = [];
        $hasError = false;
        try {
            DB::beginTransaction();

            $validator = Validator::make($this->request->all(), [
                'data' => 'required|array',
            ], [
                'data.array' => 'data phải là mảng',
                'data.required' => 'data là bắt buộc',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
            } else {
                $fields = $this->request->all();

                foreach ($fields['data'] as $value) {
                    $soNumbers = preg_split('/[\s,]+/', $value['so_numbers']); // Tách chuỗi thành mảng các số

                    foreach ($soNumbers as $soNumber) {
                        $soNumber = trim($soNumber); // Loại bỏ các khoảng trắng thừa

                        $sapData = [
                            "ID" => "1001",
                            "action_name" => "FETCH_MATERIAL_PRICE",
                            "BODY" => [
                                [
                                    "so_numbers" => $soNumber,
                                    "is_promotion" => $value['is_promotion'],
                                ]
                            ]
                        ];

                        $json = SapApiHelper::postData(json_encode($sapData));

                        if (!$json['success']) {
                            $this->errors['sap_error'] = $json['error'];
                            return null;
                        }

                        $jsonString = json_encode($json); // Convert the array to a JSON string
                        $jsonData = json_decode($jsonString, true);

                        if (!empty($jsonData['data'])) {
                            foreach ($jsonData['data'] as $index => $json_value) {
                                $material = $json_value['MATERIAL'] ?? null;
                                $price = $json_value['PRICE'] ?? null;

                                $result[] = [
                                    "so_numbers" => $soNumber,
                                    "MATERIAL" => $material,
                                    "PRICE" => $price,
                                ];
                            }
                        } else {
                            // Không tìm thấy thông tin vật liệu và giá
                            $result[] = [
                                "so_numbers" => $soNumber,
                                "MATERIAL" => null,
                                "PRICE" => null,
                            ];
                        }
                    }
                }
            }
            DB::commit();

            return $result;
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getSoSapNoteSyntax($customer_group_id)
    {
        $so_sap_note_syntax = null;
        $customer_group = CustomerGroup::find($customer_group_id);
        if ($customer_group) {
            $so_sap_note_info = json_decode($customer_group->sap_so_note_syntax, true);
            if ($so_sap_note_info) {
                // Chỉ trả về syntax khi có dùng CustomerNote
                if (isset($so_sap_note_info['key_array']) && in_array('CustomerNote', $so_sap_note_info['key_array'])) {
                    $so_sap_note_syntax['key_array'] = isset($so_sap_note_info['key_array']) ? $so_sap_note_info['key_array'] : [];
                    $so_sap_note_syntax['separators'] = isset($so_sap_note_info['separators']) ? $so_sap_note_info['separators'] : [];
                    $so_sap_note_syntax['format_data'] = isset($so_sap_note_info['format_data']) ? $so_sap_note_info['format_data'] : [];
                }
            }
        }
        return $so_sap_note_syntax;
    }
    // Check SAP code hợp lệ
    public function checkSapCode()
    {
        try {
            $validator = Validator::make($this->data, [
                'items' => 'required|array',
            ], [
                'items.array' => 'Data phải là mảng',
                'items.required' => 'Data là bắt buộc',
            ]);

            if ($validator->fails()) {
                $this->message = $validator->errors()->first();
                return false;
            }

            $check_sap_code = array_map(function($item) {
                return [
                    'is_sap_code_valid' => !empty($item['sku_sap_code']),
                ];
            }, $this->data['items']);

            return [
                'success' => true,
                'items' => $check_sap_code
            ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
}
