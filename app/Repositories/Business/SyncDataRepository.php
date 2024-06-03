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
use App\Services\Sap\SapApiHelper;
use Illuminate\Support\Facades\Log;

class SyncDataRepository extends RepositoryAbs
{
    public function syncSoHeaderFromSap()
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($this->request->all(), [
                'order_process_id' => 'required',
                'data' => 'required|array',
            ], [
                'order_process_id.required' => 'Order process là bắt buộc',
                'data.array' => 'ids phải là mảng',
                'data.required' => 'ids là bắt buộc',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
            } else {



                $fields = $this->request->all();
                $order_process_id = $this->request->input('order_process_id'); // Retrieve order_process_id
                $order_process = OrderProcess::find($order_process_id);

                $so_header = $order_process->so_headers;

                $result = [];
                $sapData = [
                    "ID" => "1001",
                    "action_name" => "CREATE_SALESORDERS",
                    "BODY" => []
                ];

                foreach ($so_header as $order) {
                    $items = [];
                    foreach ($fields['data'] as $value) {
                        if ($value['id'] == $order->id) {

                            // $sap_so_number = $order['sap_so_number'];
                            $customer_code = $order['customer_code'];
                            $ITEM_DATA = array();
                            foreach ($order->so_data_items as $item) {
                                $ITEM_DATA[] =   [
                                    "productId" =>  $item->sku_sap_code,
                                    "quantity" =>  $item->quantity3_sap,
                                    "unit" =>  $item->sku_sap_unit,
                                    "combo" => $item->is_promotive == true ? 'X' : ''
                                ];
                            }

                            $sapData['BODY'][] = [
                                "sales_org" => "3000",
                                "distr_chan" => "10",
                                "doc_type" => "ZOR",
                                "lgort" => $value["warehouse_code"],
                                "Ship_cond" => "",
                                "SO_KEY" =>  $order->id,
                                "CUST_NO" => $customer_code,
                                "VER_BOM_SALE" => "",
                                "LV2" => $order->level2,
                                "LV3" => $order->level3,
                                "LV4" =>  $order->level4,
                                "NOTE" => $order->note,
                                "USER"  => auth()->user()->name,
                                "ITEMS" => $ITEM_DATA
                            ];
                            // dd($sapData);
                        }
                    }
                }
                // dd($sapData);
                $json = SapApiHelper::postData(json_encode($sapData));

                $jsonString = json_encode($json); // Convert the array to a JSON string
                $jsonData = json_decode($jsonString, true);
                // dd($jsonData);
                foreach ($jsonData['data'] as $json_value) {
                    if ($json_value['SO_NUMBER'] != '') {

                        $soNumber = $json_value['SO_NUMBER'];
                        $soHeader = SoHeader::find($json_value['SO_KEY']);
                        $soHeader->so_uid = $soNumber;
                        $soHeader->is_sync_sap =   1;
                        $soHeader->save();
                        $result[] = [
                            "id" => $soHeader->id,
                            "so_number" => $soNumber,
                            "message" => $json_value['MESSAGE']
                        ];
                    }
                }
            }
            DB::commit();
            return  $result;
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

            $query = SoHeader::query();

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
            if ($this->request->filled('sap_so_number')) {
                $sap_so_number = $this->request->sap_so_number;
                $query->where('sap_so_number', 'LIKE', '%' . $sap_so_number . '%');
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
            $query->with([
                'order_process' => function ($query) {
                    $query->with(['customer_group' => function ($query) {
                        $query->select(['id', 'name']);
                    }]);
                },
            ]);
            $soHeader = $query->get();

            return $soHeader;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
