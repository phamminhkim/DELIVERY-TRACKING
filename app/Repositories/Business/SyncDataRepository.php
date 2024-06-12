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
        try {
            DB::beginTransaction();
            $validator = Validator::make($this->request->all(), [
                'data' => 'required|array',
            ], [
                'data.array' => 'ids phải là mảng',
                'data.required' => 'ids là bắt buộc',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
            } else {
                $fields = $this->request->all();
                $so_headers = SoHeader::whereIn('id', array_column($fields['data'], 'id'))->get();

                $sapData = [
                    "ID" => "1001",
                    "action_name" => "CREATE_SALESORDERS",
                    "BODY" => []
                ];

                foreach ($so_headers as $so_header) {
                    $order = $so_header;

                    $items = [];
                    foreach ($fields['data'] as $value) {
                        if ($value['id'] == $order->id) {
                            $ITEM_DATA = [];
                            foreach ($order->so_data_items as $item) {
                                $ITEM_DATA[] = [
                                    "productId" => $item->sku_sap_code,
                                    "quantity" => $item->quantity3_sap,
                                    "unit" => $item->sku_sap_unit,
                                    "combo" => ""
                                ];
                            }

                            $sapData['BODY'][] = [
                                "sales_org" => "3000",
                                "distr_chan" => "20",
                                "doc_type" => "ZOR",
                                "lgort" => $value["warehouse_code"],
                                "Ship_cond" => "",
                                "SO_KEY" => $order->id,
                                "CUST_NO" => $order->customer_code,
                                "VER_BOM_SALE" => "",
                                "LV2" => $order->level2,
                                "LV3" => $order->level3,
                                "LV4" => $order->level4,
                                "NOTE" => isset($value["so_sap_note"]) ? $value["so_sap_note"] : null,
                                "USER" => auth()->user()->email,
                                "ITEMS" => $ITEM_DATA
                            ];
                            // dd($sapData);
                        }
                    }
                }

                $json = SapApiHelper::postData(json_encode($sapData));


                $jsonString = json_encode($json); // Convert the array to a JSON string
                $jsonData = json_decode($jsonString, true);

                if (!empty($jsonData['data'])) {
                    foreach ($jsonData['data'] as $json_value) {
                        $soNumber = $json_value['SO_NUMBER'];
                        $soHeader = SoHeader::find($json_value['SO_KEY']);
                        // dd($json_value);
                        if ($json_value['SO_NUMBER'] != '') {
                            $soHeader->so_uid = $soNumber;
                            $soHeader->is_sync_sap = true;
                            // $soHeader->so_sap_note = $value["so_sap_note"];
                            $soHeader->so_sap_note = isset($value["so_sap_note"]) ? $value["so_sap_note"] : null;
                            $soHeader->warehouse_id = $value["warehouse_code"];
                            $soHeader->save();
                        }

                        $result[] = [
                            "id" => $soHeader->id,
                            "so_number" => $soNumber,
                            "is_sync_sap" => boolval($soHeader->is_sync_sap),
                            "so_sap_note" => $soHeader->so_sap_note,
                            "warehouse_code" => $soHeader->warehouse_id,
                            "message" => $json_value['MESSAGE']
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
    public function getSoHeader()
    {
        try {
            $user = auth()->user();
            if ($user) {
                $query = SoHeader::query();

                // Kiểm tra xem người dùng có vai trò "admin-system" hay không
                if ($this->current_user->hasRole(['admin-system'])) {
                    // Không cần lọc dữ liệu cho vai trò "admin-system"
                } else {
                    $user_id = $user->id;
                    // Lọc các bản ghi theo user_id của người dùng hiện tại trong bảng order_process
                    $query->whereHas('order_process', function ($query) use ($user_id) {
                        $query->where('created_by', $user_id);
                    });
                }
                // dd($user_id);

                // Lọc theo danh sách các ID
                if ($this->request->filled('ids')) {
                    $ids = $this->request->ids;
                    $query->whereIn('id', $ids);
                }

                // Lọc theo ngày bắt đầu
                if ($this->request->filled('from_date')) {
                    $from_date = $this->request->from_date;
                    $query->whereDate('po_delivery_date', '>=', $from_date);
                }

                // Lọc theo ngày kết thúc
                if ($this->request->filled('to_date')) {
                    $to_date = $this->request->to_date;
                    $query->whereDate('po_delivery_date', '<=', $to_date);
                }

                // Lọc theo số SAP SO
                if ($this->request->filled('so_uid')) {
                    $so_uid = $this->request->so_uid;
                    $query->where('so_uid', 'LIKE', '%' . $so_uid . '%');
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
                        $query->with(['customer_group' => function ($query) {
                            $query->select(['id', 'name']);
                        }]);
                        $query->select(['id', 'created_by', DB::raw("'" . auth()->user()->name . "' as created_by")]);
                    },
                ]);

                $query->with(['warehouse' => function ($query) {
                    $query->select('id', 'code');
                }]);

                $soHeader = $query->get();

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
}
