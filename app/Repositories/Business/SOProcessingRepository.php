<?php

namespace App\Repositories\Business;

use App\CustomerPartnerStore;
use App\Enums\OrderStatus as EnumsOrderStatus;
use App\Models\Business\Delivery;
use App\Models\Business\DeliveryToken;
use App\Models\Business\DeliveryTokenScan;
use App\Models\Business\Order;
use App\Models\Master\Customer;
use App\Models\Master\CustomerPhone;
use App\Models\Master\SapCompliance;
use App\Models\Master\SapMaterial;
use App\Models\Master\SapUnit;
use App\Models\Master\Warehouse;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SOProcessingRepository extends RepositoryAbs
{
    public function checkMaterialSap($items)
    {
        try {
            $data_clients = $this->data['items'];
            $validator = Validator::make($data_clients, [], []);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
                return false;
            }
            foreach ($data_clients as $key => $data) {
                $query_sap_material = SapMaterial::query()->with(['unit']);
                $sap_material =  $query_sap_material->whereNotNull('bar_code')
                    ->where('bar_code', $data['barcode'])->first();
                $data_clients[$key]['barcode_cty'] = $sap_material ? $sap_material->bar_code : null;
                $data_clients[$key]['sap_code'] = $sap_material ? $sap_material->sap_code : null;
                $data_clients[$key]['sap_name'] = $sap_material ? $sap_material->name : null;
                $data_clients[$key]['unit'] = $sap_material ? ($sap_material->unit_id ? $sap_material->unit->unit_code : null) : null;
            }
            // $sap_material = SapMaterial::whereIn('bar_code', $items)->get();
            return $data_clients;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function checkBookStore($items)
    {
        try {
            $data_clients = $this->data['items'];
            $validator = Validator::make($data_clients, [], []);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
                return false;
            }
            foreach ($data_clients as $key => $data) {
                $query_customer = CustomerPartnerStore::query();
                $customer = $query_customer->where('name', $data['customer_name'])->first();
                $data_clients[$key]['customer_key'] = $customer ? $customer->code : $data_clients[$key]['customer_key'];
                $data_clients[$key]['customer_name'] = $customer ? $customer->name : $data_clients[$key]['customer_name'];
            }
            return $data_clients;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function checkSapCompliance($item)
    {
        try {
            $data_clients = $this->data['items'];
            $validator = Validator::make($data_clients, [], []);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
                return false;
            }
            $key_error_sap_codes = [];
            foreach ($data_clients as $key => $data) {
                if (!empty($data['sap_code'])) {
                    $sap_material = SapCompliance::where('sap_code', $data['sap_code'])->first();
                    $data_clients[$key]['specifications'] = $sap_material ? $sap_material->compliance : null;
                    $data_clients[$key]['is_specifications'] = $sap_material ? ($sap_material->compliance > $data_clients[$key]['quantity'] ? 1 : 0) : null;
                }
            }
            return $data_clients;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function processingSOData($items)
    {
        try {
            $data_clients = $this->data['items'];
            $validator = Validator::make($data_clients, [], []);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
                return false;
            }
            // $grouped = collect($data_clients)->groupBy('sap_code');

            // Nhóm dữ liệu theo "sap_code"
            // $groupedItems = collect($data_clients)->groupBy('sap_code');
            $items = collect($data_clients);
            // 1. Lấy danh sách `customer_key` và kết hợp với các `type` nếu có
            $headers = $items->flatMap(function ($item) {
                $headers = [$item['customer_key']]; // Luôn có `customer_key` cơ bản
                if (!empty($item['type'])) { // Nếu có `type`, thêm header kết hợp
                    $headers[] = $item['customer_key'] . $item['type'];
                }
                return $headers;
            })->unique()->values()->toArray();

            // 2. Nhóm dữ liệu theo `sap_code`
            $groupedItems = $items->groupBy('sap_code');

            // 3. Chuẩn hóa dữ liệu
            $formattedItems = $groupedItems->map(function ($group) use ($headers) {
                $commonInfo = $group->first();

                // Tạo các cột `customer_key` với giá trị mặc định là `0` (hoặc null)
                $customerQuantities = array_fill_keys($headers, 0);

                // Gán số lượng (`quantity`) cho từng `customer_key` và loại (nếu có)
                foreach ($group as $item) {
                    $key = $item['customer_key'];
                    $quantityColumn = !empty($item['type']) ? "{$key}{$item['type']}" : $key;
                    // $customerQuantities[$quantityColumn] = $item['quantity'];
                    $customerQuantities[$quantityColumn] += (int)$item['quantity'];

                }
                // Gộp dữ liệu chung và các cột động
                return array_merge(
                    [
                        'promotion' => '',
                        'sap_code' => $commonInfo['sap_code'],
                        'sap_name' => $commonInfo['sap_name'],
                        'unit' => $commonInfo['unit'],
                        'price_vat' => '',
                        'specifications' => $commonInfo['specifications'],
                        'barcode' => $commonInfo['barcode'],
                        'tax' => '',
                    ],
                    $customerQuantities // Gộp các cột động vào chung kết quả
                );
            });
            $data = [
                'headers' => array_merge(['promotion', 'sap_code', 'sap_name', 'unit', 'price_vat', 'specifications', 'barcode', 'tax'], $headers),
                'items' => $formattedItems->values()->toArray(),
            ];
            return $data;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function saveSales($items)
    {
        try {
            $data_clients = $this->data['items'];
            $validator = Validator::make($data_clients, [], []);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
                return false;
            }
            $key_error_customer_codes = [];
            $key_error_sap_codes = [];
            $message = '';
            foreach ($data_clients as $key => $data) {
                if (!empty($data['customer_key'])) {
                    $check_key_customer = CustomerPartnerStore::where('code', $data['customer_key'])->first();
                    if ($check_key_customer) {
                        if ($check_key_customer->name != $data['customer_name']) {
                            CustomerPartnerStore::create([
                                'code' => $data['customer_key'],
                                'name' => $data['customer_name'],
                            ]);
                        }
                    } else {
                        CustomerPartnerStore::create([
                            'code' => $data['customer_key'],
                            'name' => $data['customer_name'],
                        ]);
                    }
                } else {
                    $key_error_customer_codes[] = $key + 1;
                }
                if ((count($key_error_customer_codes) == 0)) {
                    if (!empty($data['sap_code'])) {
                        $sap_material = SapMaterial::where('sap_code', $data['sap_code'])->first();
                        if (!$sap_material) {
                            $check_unit = SapUnit::where('unit_code', $data['unit'])->first();
                            if ($check_unit) {
                                $create_sap_material = SapMaterial::create([
                                    'bar_code' => $data['barcode'],
                                    'sap_code' => $data['sap_code'],
                                    'name' => $data['sap_name'],
                                    'unit_id' => $check_unit->id,
                                ]);
                            } else {
                                $create_sap_unit = SapUnit::create([
                                    'unit_code' => $data['sap_code'],
                                ]);
                                $create_sap_material = SapMaterial::create([
                                    'bar_code' => $data['barcode'],
                                    'sap_code' => $data['sap_code'],
                                    'name' => $data['sap_name'],
                                    'unit_id' => $create_sap_unit->id,
                                ]);
                            }
                        }
                    } else {
                        $key_error_sap_codes[] = $key + 1;
                    }
                }
            }
            if (count($key_error_customer_codes) > 0) {
                $this->errors[] = $key_error_customer_codes;
                $this->message = 'Vị trí mã tên nhà sách đang trống, vui lòng kiểm tra lại';
                return false;
            } else if (count($key_error_sap_codes) > 0) {
                $this->errors[] = $key_error_sap_codes;
                $this->message = 'Vị trí mã SAP đang trống, vui lòng kiểm tra lại';
                return false;
            }
            return $data_clients;
            // return $order;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
