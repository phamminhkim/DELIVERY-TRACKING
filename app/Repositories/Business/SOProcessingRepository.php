<?php

namespace App\Repositories\Business;

use App\CustomerPartnerStore;
use App\Enums\OrderStatus as EnumsOrderStatus;
use App\Models\Business\Delivery;
use App\Models\Business\DeliveryToken;
use App\Models\Business\DeliveryTokenScan;
use App\Models\Business\Order;
use App\Models\Business\OrderProcess;
use App\Models\Master\Customer;
use App\Models\Master\CustomerPhone;
use App\Models\Master\SapCompliance;
use App\Models\Master\SapMaterial;
use App\Models\Master\SapUnit;
use App\Models\Master\Warehouse;
use App\OrderProcessSale;
use App\OrderProcessSaleBy;
use App\OrderProcessSaleItem;
use App\OrderProcessSaleReceive;
use App\Repositories\Abstracts\RepositoryAbs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use stdClass;

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
            $newObject = new stdClass();
            foreach ($headers as $value) {
                $newObject->$value = null; // Gán giá trị mong muốn, mặc định là null
                $newObject->promotion = null;
                $newObject->sap_code = null;
                $newObject->sap_name = null;
                $newObject->unit = null;
                $newObject->price_vat = null;
                $newObject->specifications = null;
                $newObject->barcode = null;
                $newObject->tax = null;
                $newObject->sum_quantity = null;
            }
            // 3. Chuẩn hóa dữ liệu
            $formattedItems = $groupedItems->map(function ($group) use ($headers, $newObject) {
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
                // nếu cột nào == 0 thì cho bằng null
                foreach ($customerQuantities as $key => $value) {
                    if ($value == 0) {
                        $customerQuantities[$key] = null;
                    }
                }

                // Gộp dữ liệu chung và các cột động
                return array_merge(
                    [
                        'promotion' => '',
                        'sap_code' => $commonInfo['sap_code'],
                        'sap_name' => $commonInfo['sap_name'],
                        'unit' => $commonInfo['unit'],
                        'price_vat' => '11880',
                        'specifications' => $commonInfo['specifications'],
                        'barcode' => $commonInfo['barcode'],
                        'tax' => '0.08',
                        'sum_quantity' => $group->sum('quantity'),
                        'color' => [
                            'background' => $newObject,
                            'text' => $newObject
                        ]
                        // 'color' => [
                        //     'background' => '#ffcccc',
                        //     'text' => '#000000',
                        // ]

                    ],
                    $customerQuantities // Gộp các cột động vào chung kết quả
                );
            });
            $fixedHeaders = [
                'promotion' => 'Phân loại',
                'sap_code' => 'Mã SAP',
                'sap_name' => 'Tên sản phẩm',
                'unit' => 'Đơn vị',
                'price_vat' => 'Giá chưa VAT',
                'specifications' => 'Quy cách',
                'barcode' => 'Mã vạch',
                'tax' => 'Thuế',
                'sum_quantity' => 'Tổng SL',
                // 'color' => 'Màu'
                'color' => [
                    'background' => $newObject,
                    'text' => $newObject
                ]

            ];
            $fixed_header_addtion = [
                'promotion' => '',
                'sap_code' => '',
                'sap_name' => '',
                'unit' => '',
                'price_vat' => '',
                'specifications' => '',
                'barcode' => '',
                'tax' => '',
                'sum_quantity' => '',
                'color' => [
                    'background' => $newObject,
                    'text' => $newObject
                ]
            ];
            // Tạo custom_headers với giá trị rỗng ''
            $custom_headers = array_fill_keys($headers, '');

            // Ví dụ: Thêm hậu tố (SL) vào tên header
            $custom_headers = array_map(function ($header) {
                return ''; // Giá trị đều là ''
            }, array_fill_keys($headers, ''));

            // $headersKeyValue = array_merge($fixedHeaders, array_combine($headers, $headers));
            // $formattedItems->prepend($headersKeyValue, 'headers');


            $additional_header_row_null = array_fill_keys(array_keys($fixedHeaders), ''); // Hàng rỗng
            // Custom row 1 (trên Ký hiệu ĐH)
            $additional_header_row_1 = array_fill_keys(array_keys($fixedHeaders), ''); // Hàng rỗng
            $additional_header_row_1['sum_quantity'] = 'T_Tiền (-VAT)';
            $additional_header_row_1['tax'] = 'Số lượng NS';

            // Custom row 2 (Ký hiệu ĐH)
            $additional_header_row_2 = array_fill_keys(array_keys($fixedHeaders), '');
            $additional_header_row_2['sum_quantity'] = 'Ký hiệu ĐH';
            // $additional_header_row_2['tax'] = count($headers); // Nếu có cột động thì hiển thị 'Tổng'
            $additional_header_row_null['tax'] = count($headers); // Nếu có cột động thì hiển thị 'Tổng'

            // Tạo header đầy đủ bao gồm các cột động
            $headersKeyValue = array_merge($fixedHeaders, array_combine($headers, $headers));
            $headersKeyValue_1 = array_merge($additional_header_row_1, array_combine($headers, $custom_headers));
            $headersKeyValue_null = array_merge($additional_header_row_null, array_combine($headers, $custom_headers));
            $headersKeyValue_2 = array_merge($additional_header_row_2, array_combine($headers, $custom_headers));

            // Gắn hàng mới vào $formattedItems
            $formattedItems->prepend($headersKeyValue, 'headers'); // Header chính
            $formattedItems->prepend($headersKeyValue_2, 'additional_header_row_2'); // Header mới
            $formattedItems->prepend($headersKeyValue_null, '   '); // Header mới
            $formattedItems->prepend($headersKeyValue_1, 'additional_header_row_1'); // Header mới


            $data = [
                // 'headers' => array_merge(['promotion', 'sap_code', 'sap_name', 'unit', 'price_vat', 'specifications', 'barcode', 'tax'], $headers),
                'headers' => $headersKeyValue,
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

            $data_client_header  = $this->data['so_header'];
            $data_clients = $this->data['items'];
            $validator = Validator::make(
                ['items' => $data_clients], // Bọc lại dữ liệu trong key "items"
                [
                    'items.*.sap_code' => 'required',
                ],
                [
                    'items.*.sap_code.required' => ':attribute không được để trống',

                ]
            );
            $is_error = false;
            $genarate_order_id = $this->generateOrderId();
            $check_code = $this->checkCode($genarate_order_id);
            if (!$check_code) {
                $is_error = true;
            }
            if ($validator->fails() || $is_error) {
                $this->errors = $validator->errors()->all();
                // $this->errors = ['indexs' => $validator->errors()->all()];
                $this->message = 'Mã SAP không được để trống. Vui lòng kiểm tra lại.';
                return false;
            } else {
                $key_error_customer_codes = [];
                $key_error_sap_codes = [];
                $message = '';

                foreach ($data_clients as $key => $data) {
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
                if (!is_array($data_client_header) || !isset($data_client_header['user_receiver_maile_id']) &&  $data_client_header['status'] !== 'pending') {
                    $this->errors[] = $data_client_header;
                    $this->message = 'Vui lòng chọn người nhận';
                    return false;
                } else {
                    $OrderProcessSale = OrderProcessSale::create([
                        'title' => $data_client_header['title'],
                        'code' => $genarate_order_id,
                        'central_branch' => $data_client_header['central_branch'],
                        'description' => $data_client_header['description'],
                        'status' => $data_client_header['status'],
                        'created_by' => auth()->id(),
                    ]);
                }

                if ($OrderProcessSale) {
                    foreach ($data_clients as $key => $data) {
                        $OrderProcessSaleItem = OrderProcessSaleItem::create([
                            'order_process_sale_id' => $OrderProcessSale->id,
                            'customer_key' => $data['customer_key'],
                            'type' => $data['type'] ?? null,
                            'customer_name' => $data['customer_name'],
                            'barcode' => $data['barcode'],
                            'product_name' => $data['product_name'],
                            'price' => $data['price'] ?? 0,
                            'quantity' => $data['quantity'],
                            'specifications' => $data['specifications'],
                            'description' => $data['description'],
                            'count_order' => $data['count_order'],
                            'barcode_cty' => $data['barcode_cty'],
                            'sap_code' => $data['sap_code'],
                            'sap_name' => $data['sap_name'],
                            'unit' => $data['unit'],
                        ]);
                    };
                    if (isset($data_client_header['user_receiver_maile_id']) && $data_client_header['user_receiver_maile_id']) {
                        if ($data_client_header['status'] == 'pending') {
                            OrderProcessSaleReceive::create([
                                'order_process_sale_id' => $OrderProcessSale->id,
                                'received_by' => $data_client_header['user_receiver_maile_id'],
                                'received_at' => null,
                            ]);
                        } else {
                            OrderProcessSaleReceive::create([
                                'order_process_sale_id' => $OrderProcessSale->id,
                                'received_by' => $data_client_header['user_receiver_maile_id'],
                                'received_at' => now(),
                            ]);
                        }
                    }
                }
                $load_items = $OrderProcessSale->load('orderProcessSaleItems');
                return $load_items;

                // return $data_clients;
                // return $order;
            }
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function updateSaveSales($items, $id)
    {
        try {
            $data_client_header  = $this->data['so_header'];
            $data_clients = $this->data['items'];
            $validator = Validator::make($data_client_header, [
                'title' => 'required'
            ], []);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $find_order_process_sale = OrderProcessSale::find($id);
                if ($find_order_process_sale) {
                    $find_order_process_sale->orderProcessSaleItems()->delete();
                    $find_order_process_sale->update([
                        'title' => $data_client_header['title'],
                        'central_branch' => $data_client_header['central_branch'],
                        'description' => $data_client_header['description'],
                        'status' => $data_client_header['status'],
                        'created_by' => auth()->id(),
                    ]);
                    foreach ($data_clients as $key => $data) {
                        $OrderProcessSaleItem = OrderProcessSaleItem::create([
                            'order_process_sale_id' => $find_order_process_sale->id,
                            'customer_key' => $data['customer_key'],
                            'type' => $data['type'] ?? null,
                            'customer_name' => $data['customer_name'],
                            'barcode' => $data['barcode'],
                            'product_name' => $data['product_name'],
                            'price' => $data['price'] ?? 0,
                            'quantity' => $data['quantity'],
                            'specifications' => $data['specifications'],
                            'description' => $data['description'],
                            'count_order' => $data['count_order'],
                            'barcode_cty' => $data['barcode_cty'],
                            'sap_code' => $data['sap_code'],
                            'sap_name' => $data['sap_name'],
                            'unit' => $data['unit'],
                        ]);
                    }
                }
                $load_items = $find_order_process_sale->load('orderProcessSaleItems');
                // dd($load_items);
                return $load_items;
            }
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteOrderPOSales($items, $id)
    {
        try {
            $find_order_process_sale = OrderProcessSale::find($id);
            $is_error = false;

            if ($find_order_process_sale) {
                if ($find_order_process_sale->status == 'completed') {
                    $this->errors[] = $id;
                    $this->message = 'Đơn hàng đã được xử lý hoàn tất. Không thể xóa';
                    $is_error = true;
                } else if ($find_order_process_sale->status == 'processing') {
                    $this->errors[] = $id;
                    $this->message = 'Đơn hàng đang được xử lý. Không thể xóa';
                    $is_error = true;
                } else if ($find_order_process_sale->status == 'sending') {
                    $this->errors[] = $id;
                    $this->message = 'Đơn hàng đã gửi xử lý. Không thể xóa';
                    $is_error = true;
                }
            }
            if ($is_error) {
                $this->errors[] = $id;
                return false;
            } else {
                $find_order_process_sale->orderProcessSaleItems()->delete();
                $find_order_process_sale->orderProcessSaleBy()->delete();
                $find_order_process_sale->orderProcessSaleReceive()->delete();
                $find_order_process_sale->delete();
                return $find_order_process_sale;
            }
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }


    public function getStatusPending($items)
    {
        try {
            $query = OrderProcessSale::query();
            $query = $query->whereHas('orderProcessSaleReceive', function ($query) {
                $query->where('received_by', auth()->id());
            });
            $order = $query->where('status', 'sending')->count();
            return $order;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getAllOrderPOSales($items)
    {
        try {
            $auth = auth()->id();
            $query = OrderProcessSale::query()->with(['orderProcessSaleBy.user', 'user', 'orderProcessSaleItems']);
            $query = $query->where('status', '!=', 'pending');
            $query = $query->whereHas('orderProcessSaleReceive', function ($query) use ($auth) {
                $query->where('received_by', $auth);
            });
            $order = $query->get();
            return $order;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getAllOrderPOSalesCreateBy($items)
    {
        try {
            $auth = auth()->id();
            $query = OrderProcessSale::query()->with(['orderProcessSaleBy.user', 'user', 'orderProcessSaleReceive.received']);
            $query = $query->where('created_by', $auth);
            // sắp xếp theo ngày tạo mới nhất
            $query->orderBy('created_at', 'desc');
            $order = $query->get();
            return $order;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getAllOrderPOSalesCreateByID($items, $id)
    {
        try {
            // $query = OrderProcessSale::query()->with(['orderProcessSaleBy.user', 'user', 'orderProcessSaleItems']);
            // $query = $query->where('id', $id);
            // $order = $query->get();
            $find_order_process_sale = OrderProcessSale::find($id)->load('orderProcessSaleBy.user', 'user', 'orderProcessSaleItems');
            return $find_order_process_sale;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function applyOrderPOsales($items)
    {
        try {

            $data_clients = $this->data['item'];
            $data_status = $this->data['status'];
            $auth = auth()->id();
            $validator = Validator::make($data_clients, [], []);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $check_order_process_by = OrderProcessSaleBy::where('order_process_sale_id', $data_clients['id'])->first();
                if (!$check_order_process_by) {
                    $OrderProcessSaleBy = OrderProcessSaleBy::create([
                        'order_process_sale_id' => $data_clients['id'],
                        'processing_by' => auth()->id(),
                        'processing_at' => now(),
                    ]);
                    $update_order_process = OrderProcessSale::where('id', $data_clients['id'])->update([
                        'status' => $data_status,
                    ]);
                } else {
                    if ($check_order_process_by->processing_by != $auth) {
                        $this->errors[] = $data_clients;
                        $this->message = 'Đơn hàng đã được người khác xử lý';
                        return false;
                    } else {
                        // $check_order_process_by = OrderProcessSale::where('id', $data_clients['id'])->first();
                        // if ($check_order_process_by->status == 'completed') {
                        //     $this->errors[] = $data_clients;
                        //     $this->message = 'Đơn hàng đã được xử lý hoàn tất';
                        //     return false;
                        // } else if ($check_order_process_by->status == 'canceled') {
                        //     $this->errors[] = $data_clients;
                        //     $this->message = 'Đơn hàng đã được hủy';
                        //     return false;
                        // } else if ($check_order_process_by->status == 'processing') {
                        //     $this->errors[] = $data_clients;
                        //     $this->message = 'Đơn hàng đang được xử lý';
                        //     return false;
                        // }

                        if ($data_status == 'completed') {
                            // $data_status
                            $update_order_process = OrderProcessSale::where('id', $data_clients['id'])->update([
                                'status' => $data_status,
                            ]);
                            $update_order_process_by = OrderProcessSaleBy::where('order_process_sale_id', $data_clients['id'])->update([
                                'completed_at' => now(),
                            ]);
                        } else if ($data_status == 'canceled') {
                            $update_order_process = OrderProcessSale::where('id', $data_clients['id'])->update([
                                'status' => $data_status,
                                // 'completed_at' => now(),
                            ]);
                            $update_order_process_by = OrderProcessSaleBy::where('order_process_sale_id', $data_clients['id'])->update([
                                'completed_at' => now(),
                            ]);
                        } else if ($data_status == 'processing') {
                            $update_order_process = OrderProcessSale::where('id', $data_clients['id'])->update([
                                'status' => $data_status,
                            ]);
                            OrderProcessSaleBy::where('order_process_sale_id', $data_clients['id'])->update([
                                'processing_at' => now(),
                            ]);
                        }
                        // else if ($data_clients['status'] == 'sending') {
                        //     $this->errors[] = $data_clients;
                        //     $this->message = 'Đơn hàng đã được xử lýádasd';
                        //     return false;
                        // }
                    }
                }
                DB::commit();
                return $data_clients;
            }

            // return $order;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function sendingOrderPOsales($items, $id)
    {
        try {
            $data_clients = $this->data['item'];
            $data_status = $this->data['status'];
            $auth = auth()->id();
            $validator = Validator::make($data_clients, [], []);
            $is_error = false;
            if ($data_clients['order_process_sale_receive'] === null) {
                $this->errors[] = $data_clients;
                $this->message = 'Vui lòng chọn người nhận';
                $is_error = true;
            }
            if ($validator->fails() || $is_error) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $find_order_process_sale = OrderProcessSale::find($id);
                if ($find_order_process_sale) {
                    if ($find_order_process_sale->created_by == $auth) {
                        $update_order_process = OrderProcessSale::where('id', $id)->update([
                            'status' => $data_status,
                        ]);
                        $find_id = OrderProcessSaleReceive::where('order_process_sale_id', $id)->exists();
                        if ($find_id) {
                            $update_order_process_by = OrderProcessSaleReceive::where('order_process_sale_id', $id)->update([
                                'received_by' => $data_clients['order_process_sale_receive']['id'],
                                'received_at' => now(),
                            ]);
                        } else {
                            OrderProcessSaleReceive::create([
                                'order_process_sale_id' => $id,
                                'received_by' => $data_clients['order_process_sale_receive']['id'],
                                'received_at' => now(),
                            ]);
                        }
                    } else {
                        $this->errors[] = $data_clients;
                        $this->message = 'Bạn không có quyền thực hiện';
                        return false;
                    }
                }
                DB::commit();
                return true;
            }
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function generateOrderId()
    {
        $today = Carbon::today()->format('Y-m-d');

        $sequence = OrderProcessSale::whereDate('created_at', $today)->count();
        if ($sequence == 0) {
            $sequence_number = 1;
        } else {
            $sequence_number = $sequence + 1;
        }
        $order_id = sprintf('ORD-%s-%d', Carbon::today()->format('Ymd'), $sequence_number);
        return $order_id;
    }
    public function checkCode($order_code)
    {
        try {
            $today = Carbon::today()->format('Y-m-d');
            $find_code = OrderProcessSale::where('created_at', $today)
                ->where('code', $order_code)->first();
            if ($find_code) {
                $this->errors[] = $order_code;
                $this->message = 'Mã đơn hàng đã tồn tại';
                return false;
            }
            return true;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getOrderStatusStatics($items)
    {
        try {
            $query = OrderProcessSale::query();
            // $query = $query->whereHas('orderProcessSaleReceive', function ($query) {
            //     $query->where('received_by', auth()->id());
            // });
            $query = $query->where('created_by', auth()->id());
            $order = $query->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get();
            return $order;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
