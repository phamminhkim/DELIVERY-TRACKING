<?php

namespace App\Repositories\Business;

use App\Enums\OrderStatus as EnumsOrderStatus;
use App\Models\Business\Order;
use App\Models\Master\Customer;
use App\Models\Master\Warehouse;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderRepository extends RepositoryAbs
{
    public function syncOrderFromSAP()
    {
        try {
            $validator = Validator::make($this->data, [
                '*.company_code' => 'required|string|exists:companies,code',
                '*.customer_code' => 'required|string|exists:customers,code',
                '*.sap_so_number' => 'required|string|max:20',
                '*.sap_so_created_date' => 'required|date',
                '*.sap_po_number' => 'nullable|string|max:20',
                '*.sap_do_number' => 'nullable|string|max:20',
                '*.approveds.sap_so_finance_approval_date' => 'required|date',
                '*.details.delivery_address' => 'nullable|string|max:255',
                '*.details.note' => 'required|string|max:200',
                '*.details.total_item' => 'required|numeric',
                '*.details.total_weight' => 'required|numeric',
                '*.details.total_value' => 'required|numeric',
                '*.receivers.receiver_name' => 'required|string|max:255',
                '*.receivers.receiver_phone' => 'nullable|string|max:255',
                '*.warehouse_code' => 'required|string|exists:warehouses,code',
                '*.is_deleted' => 'nullable|boolean'
            ], [
                '*.company_code.required' => 'Mã công ty là bắt buộc.',
                '*.company_code.exists' => 'Mã công ty không tồn tại.',
                '*.customer_code.required' => 'Mã khách hàng là bắt buộc.',
                '*.customer_code.exists' => 'Mã khách hàng không tồn tại.',
                '*.sap_so_number.required' => 'Số đơn hàng SAP là bắt buộc.',
                '*.sap_so_number.max' => 'Số đơn hàng SAP không được vượt quá 20 ký tự.',
                '*.sap_so_created_date.required' => 'Ngày tạo đơn hàng SAP là bắt buộc.',
                '*.sap_so_created_date.date' => 'Ngày tạo đơn hàng SAP không hợp lệ.',
                '*.sap_po_number.max' => 'Số đơn hàng PO không được vượt quá 20 ký tự.',
                '*.sap_do_number.max' => 'Số đơn hàng DO không được vượt quá 20 ký tự.',
                '*.approveds.sap_so_finance_approval_date.required' => 'Ngày phê duyệt tài chính SAP SO là bắt buộc.',
                '*.approveds.sap_so_finance_approval_date.date' => 'Ngày phê duyệt tài chính SAP SO không hợp lệ.',
                '*.details.delivery_address.max' => 'Địa chỉ giao hàng không được vượt quá 255 ký tự.',
                '*.details.note.required' => 'Ghi chú là bắt buộc.',
                '*.details.note.max' => 'Ghi chú không được vượt quá 200 ký tự.',
                '*.details.total_item.required' => 'Tổng số mục là bắt buộc.',
                '*.details.total_item.numeric' => 'Tổng số mục phải là số.',
                '*.details.total_weight.required' => 'Tổng khối lượng là bắt buộc.',
                '*.details.total_weight.numeric' => 'Tổng khối lượng phải là số.',
                '*.details.total_value.required' => 'Tổng giá trị là bắt buộc.',
                '*.details.total_value.numeric' => 'Tổng giá trị phải là số.',
                '*.receivers.receiver_name.required' => 'Tên người nhận là bắt buộc.',
                '*.receivers.receiver_name.max' => 'Tên người nhận không được vượt quá 255 ký tự.',
                '*.receivers.receiver_phone.max' => 'Số điện thoại người nhận không được vượt quá 255 ký tự.',
                '*.warehouse_code.required' => 'Mã kho là bắt buộc.',
                '*.warehouse_code.exists' => 'Mã kho không tồn tại.',
                '*.is_deleted.boolean' => 'Trạng thái xóa phải là true hoặc false.'
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $result = array(
                    'insert_count' => 0,
                    'update_count' => 0,
                    'skip_count' => 0,
                    'delete_count' => 0,
                    'error_count' => 0,
                );

                DB::beginTransaction();
                foreach ($this->data as $order) {
                    if ($order['is_deleted']) {
                        $order = Order::where('sap_so_number', $order['sap_so_number'])->first();
                        if ($order) {
                            $order->delete();
                            $result['delete_count']++;
                        } else {
                            $result['skip_count']++;
                        }
                    } else {
                        $customer = Customer::where('code', $order['customer_code'])->first();
                        if (!$customer) {
                            $result['error_count']++;
                            $this->errors[] = 'Không tìm thấy khách hàng có mã ' . $order['customer_code'];
                            continue;
                        }
                        $warehouse = Warehouse::where('code', $order['warehouse_code'])->first();
                        if (!$warehouse) {
                            $result['error_count']++;
                            $this->errors[] = 'Không tìm thấy kho có mã ' . $order['warehouse_code'];
                            continue;
                        }

                        $created_order = Order::updateOrCreate(
                            [
                                'sap_so_number' => $order['sap_so_number']
                            ],
                            [
                                'company_code' => $order['company_code'],
                                'customer_id' => $customer->id,
                                'sap_so_created_date' => $order['sap_so_created_date'],
                                'sap_po_number' => $order['sap_po_number'] ?? '',
                                'sap_do_number' => $order['sap_do_number'] ?? '',
                                'status_id' => EnumsOrderStatus::Pending,
                                'warehouse_id' => $warehouse->id,
                            ]
                        );
                        $created_order->approved->updateOrCreate(['order_id' => $created_order['id']], $order['approveds']);
                        $created_order->detail->updateOrCreate(['order_id' => $created_order['id']], [
                            'delivery_address' => $order['details']['delivery_address'] ?? '',
                            'note' => $order['details']['note'] ?? '',
                            'total_item' => $order['details']['total_item'],
                            'total_weight' => $order['details']['total_weight'],
                            'total_value' => $order['details']['total_value'],
                        ]);
                        $created_order->receiver->updateOrCreate(['order_id' => $created_order['id']], [
                            'receiver_name' => $order['receivers']['receiver_name'],
                            'receiver_phone' => $order['receivers']['receiver_phone'] ?? '',
                            'note' => $order['receivers']['note'] ?? '',
                        ]);

                        if ($created_order->wasRecentlyCreated || $created_order->getChanges()) {
                            $result['insert_count']++;
                        } else {
                            $result['update_count']++;
                        }
                    }
                }
                DB::commit();
                return $result;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
