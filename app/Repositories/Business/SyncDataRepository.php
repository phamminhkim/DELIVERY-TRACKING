<?php

namespace App\Repositories\Business;

use App\Repositories\Abstracts\RepositoryAbs;
use App\Models\Business\SoHeader;
use App\Models\Business\SoDataItem;
use App\Models\Business\OrderProcess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Enums\File\FileStatuses;
use App\Models\Business\FileStatus;
use App\Models\Master\Warehouse;
use App\Services\Sap\SapApiHelper;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;
use Spatie\Permission\Models\Role;

class SyncDataRepository extends RepositoryAbs
{
    public function syncSoHeaderFromSap()
    {
        $result = [];
        $totalSyncedOrders = 0; // Biến để lưu số đơn hàng đã đồng bộ thành công
        $sync_order = "";
        $validator = Validator::make($this->request->all(), [
            'data' => 'required|array',
        ], [
            'data.array' => 'ids phải là mảng',
            'data.required' => 'ids là bắt buộc',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return null;
        }

        $fields = $this->request->all();
        $so_headers = SoHeader::whereIn('id', array_column($fields['data'], 'id'))->get();
        $warehouse = Warehouse::where('id', array_column($fields['data'], 'id'))->first();

        // Bật trạng thái đang sync data
        $this->changeIsSyncingSapSoHeader($so_headers, true);
        try {
            DB::beginTransaction();
            $sapData = [
                "ID" => "1001",
                "action_name" => "CREATE_SALESORDERS",
                "BODY" => []
            ];
            //chi lay nhung so_header chua dong bo
            $not_sync_so_headers = [];
            foreach ($so_headers as $so_header) {
                if (!$so_header->sync_sap_status) {
                    $not_sync_so_headers[] = $so_header;
                }
            }

            foreach ($not_sync_so_headers as $so_header) {
                $order = $so_header;
                $items = [];

                foreach ($fields['data'] as $value) {
                    if ($value['id'] == $order->id) {
                        $ITEM_DATA = [];
                        foreach ($order->so_data_items as $item) {
                            if (!$item->sku_sap_code) {
                                // Lưu vào danh sách lỗi thiếu mã SAP
                                $result[] = [
                                    "id" => $value['id'],
                                    "so_number" => null,
                                    "sync_sap_status" => 0,
                                    "so_sap_note" => $value["so_sap_note"],
                                    "warehouse_code" => $value['warehouse_code'],
                                    "Ship_cond" => $value["Ship_cond"],
                                    "message" => "Đơn hàng có sản phẩm chưa mapping mã SAP!"
                                ];
                                break;
                            }
                            if ($item->is_inventory == 0) {
                                $ITEM_DATA[] = [
                                    "productId" => $item->sku_sap_code,
                                    "quantity" => $item->quantity3_sap,
                                    "unit" => $item->sku_sap_unit,
                                    "combo" => ""
                                ];
                            }
                        }
                        $warehouse_id = $value['warehouse_code'];
                        $warehouse = Warehouse::where('id', $warehouse_id)->first();
                        if ($warehouse == null) {
                            $warehouse_code = "3101";
                        } else {
                            $warehouse_code = $warehouse->code;
                        }
                        $group_name = isset($value["promotive_name"]) ? $order->sap_so_number . $value["promotive_name"] : $order->sap_so_number;
                        if (strlen($group_name) > 35) {
                            $group_name = mb_substr($group_name, 0, 35);
                        }
                        $sapData['BODY'][] = [
                            "sales_org" => "3000",
                            "distr_chan" => "20",
                            "doc_type" => "ZOR",
                            "lgort" => $warehouse_code,
                            "Ship_cond" => isset($value["Ship_cond"]) ? $value["Ship_cond"] : null,
                            "SO_KEY" => $order->id,
                            "GROUP_NAME" => $group_name, //$order->sap_so_number, //isset($value["so_sap_note"]) ? $value["so_sap_note"] : null,
                            "CUST_NO" => $order->customer_code,
                            "VER_BOM_SALE" => "",
                            "LV2" => $order->level2,
                            "LV3" => $order->level3,
                            "LV4" => $order->level4,
                            "NOTE" => isset($value["so_sap_note"]) ?
                                (isset($value["promotive_name"]) ? $value["so_sap_note"] . $value["promotive_name"] : $value["so_sap_note"])
                                : null,
                            "USER" => auth()->user()->email,
                            "ITEMS" => $ITEM_DATA
                        ];
                    }
                }
            }
            // dd($sapData);
            $json = SapApiHelper::postData(json_encode($sapData));

            $jsonString = json_encode($json); // Convert the array to a JSON string
            $jsonData = json_decode($jsonString, true);
            // dd($json);

            // Kiểm tra lỗi kết nối đồng bộ
            if (!$jsonData['success']) {
                $this->errors['sap_error'] = $jsonData['error'];
                $result = null;
            } else {
                if (!empty($jsonData['data'])) {
                    foreach ($jsonData['data'] as $json_value) {
                        $soNumber = $json_value['SO_NUMBER'];
                        // Log::info($soNumber);
                        $sync_sap_status = 0;
                        $so_sap_note = '';
                        $warehouse_id = 0;
                        $shipping_id = 0;
                        if (($json_value['SO_KEY'] != '') && ($json_value['SO_NUMBER'] != '')) {
                            //lấy giá trị trong mảng $fields['data'] theo  id
                            $value_item = null;
                            foreach ($fields['data'] as $value) {
                                if ($value['id'] == $json_value['SO_KEY']) {
                                    $value_item = $value;
                                    continue; //thoát vòng lặp
                                }
                            }
                            // dd($value_item);
                            $soHeader = SoHeader::find($json_value['SO_KEY']);
                            $soHeader->so_uid = $soNumber;
                            $soHeader->sync_sap_status = 1;
                            //  $soHeader->so_sap_note = isset($value["so_sap_note"]) ? $value["so_sap_note"] : null;
                            $soHeader->warehouse_id = $value_item["warehouse_code"];
                            $soHeader->shipping_id = isset($value_item["Ship_cond"]) ? $value_item["Ship_cond"] : null;
                            $sync_sap_status = $soHeader->sync_sap_status = 1;
                            $so_sap_note = $soHeader->so_sap_note;
                            $warehouse_id = $soHeader->warehouse_id;
                            $shipping_id = $soHeader->shipping_id;
                            $soHeader->save();
                            $totalSyncedOrders++;
                            // dd($json_value);
                        } else if ($json_value['SO_KEY'] == '') {
                            $this->errors['not_config_user'] = $json_value['MESSAGE'];
                            $result = null;
                            break;
                        }
                        $result[] = [
                            "id" => $json_value['SO_KEY'],
                            "so_number" => $soNumber,
                            "sync_sap_status" => $sync_sap_status,
                            "so_sap_note" => $so_sap_note,
                            "warehouse_code" => $warehouse_id,
                            "Ship_cond" => $shipping_id,
                            "message" => $json_value['MESSAGE']
                        ];
                    }
                    // Tính tổng số lượng đơn hàng đã đồng bộ thành công
                    $sync_order =  $totalSyncedOrders;
                } else {
                    $this->errors['synchronized_error'] = "Các đơn hàng đã được đồng bộ trước đó";
                    // $result = null;
                }
            }
            DB::commit();
            $this->changeIsSyncingSapSoHeader($so_headers, false);

            return [
                // 'success' => true,
                'data' => $result,
                'total_sync_order' => $sync_order
            ];
            // return $result;
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            $this->changeIsSyncingSapSoHeader($so_headers, false);
        }
    }
    public function getSoHeader()
    {
        try {
            $user = auth()->user();
            if ($user) {
                $query = SoHeader::query();

                // Kiểm tra vai trò admin-system
                if (!$this->current_user->hasRole(['admin-system'])) {
                    $user_id = $user->id;
                    // Lọc các bản ghi theo user_id của người dùng hiện tại trong bảng order_process
                    $query->whereHas('order_process', function ($query) use ($user_id) {
                        $query->where('created_by', $user_id);
                    });
                }
                $query->whereHas('order_process', function ($query) {
                    $query->where('is_deleted', 0); // Chỉ lấy những order_process có is_deleted = 0
                });

                // Lọc theo danh sách các ID
                if ($this->request->filled('ids')) {
                    $ids = $this->request->ids;
                    $query->whereIn('id', $ids);
                }

                // Xử lý from_date và to_date
                if ($this->request->filled('from_date')) {
                    $from_date = $this->request->from_date;
                    $query->whereDate('created_at', '>=', $from_date);
                }
                if ($this->request->filled('to_date')) {
                    $to_date = $this->request->to_date;
                    $query->whereDate('created_at', '<=', $to_date);
                }

                // Lọc theo số SAP SO
                if ($this->request->filled('so_uid')) {
                    $so_uid = $this->request->so_uid;
                    $query->where('so_uid', 'LIKE', '%' . $so_uid . '%');
                }

                // Lọc theo order_process_id
                if ($this->request->filled('order_process_id')) {
                    $order_process_ids = $this->request->order_process_id;
                    $query->whereIn('order_process_id', $order_process_ids);
                }

                // Lọc theo số PO
                if ($this->request->filled('po_number')) {
                    $po_number = $this->request->po_number;
                    $query->where('po_number', 'LIKE', '%' . $po_number . '%');
                }

                // Lọc theo tên khách hàng
                if ($this->request->filled('customer_name')) {
                    $customer_name = $this->request->customer_name;
                    $query->where('customer_name', 'LIKE', '%' . $customer_name . '%');
                }

                // Lọc theo mã khách hàng
                if ($this->request->filled('customer_code')) {
                    $customer_code = $this->request->customer_code;
                    $query->where('customer_code', 'LIKE', '%' . $customer_code . '%');
                }

                // Lọc theo danh sách customer_group_ids
                if ($this->request->filled('customer_group_ids')) {
                    $customer_group_ids = $this->request->customer_group_ids;
                    $query->whereHas('order_process', function ($query) use ($customer_group_ids) {
                        $query->whereIn('customer_group_id', $customer_group_ids);
                    });
                }
                // Sắp xếp theo ID, ID cuối cùng được hiển thị trước
                $query->orderByDesc('id');

                // Hiển thị danh sách nhóm khách hàng từ bảng order_process

                $query->with([
                    'order_process' => function ($query) {
                        $query->with([
                            'customer_group:id,name',
                            'created_by:id,name',
                        ]);
                    },
                    'warehouse:id,code',
                    'so_data_items',
                ])->select(['*', DB::raw("DATE(created_at) as create_at"), DB::raw("DATE(updated_at) as update_at")]);

                // Truy vấn dữ liệu
                $soHeader = $query->get();

                // Lấy trường promotive_name để xử lý SAP note, SO name
                // Xử lý promotive_name từ so_data_items
                foreach ($soHeader as $item) {
                    $item->promotive_name = $item->so_data_items->count() > 0 ? $item->so_data_items->first()->promotive_name : '';
                    unset($item->so_data_items);
                }

                return $soHeader;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function getSoHeaderByIds()
    {
        try {
            $validator = Validator::make($this->data, [
                'items' => 'required|array',
                'items.*.id' => 'required',
            ], [
                'items.required' => 'items là bắt buộc',
                'items.array' => 'items phải là mảng',
                'items.*.id.required' => 'id là bắt buộc',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $so_headers = [];
                foreach ($this->data['items'] as $item) {
                    $ids = [$item['id']];
                    $so_header = SoHeader::whereIn('id', $ids)
                        ->with(['so_data_items', 'order_process.customer_group:id,name'])
                        ->first();

                    if ($so_header) {
                        $so_header->so_data_items = SoDataItem::where('so_header_id', $so_header->id)->get();
                        $so_headers[] = $so_header;
                    }
                }
                DB::commit();
                return $so_headers;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    // Hàm change trạng thái is_syncing_sap của so_header
    public function changeIsSyncingSapSoHeader($so_headers, $is_syncing_sap = false)
    {
        try {
            DB::beginTransaction();
            foreach ($so_headers as $item) {
                $so_header = SoHeader::find($item->id);
                if ($so_header) {
                    $so_header->is_syncing_sap = $is_syncing_sap;
                    $so_header->save();
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
}
