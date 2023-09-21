<?php

namespace App\Repositories\Business;

use App\Enums\OrderStatus as EnumsOrderStatus;
use App\Jobs\SendPreparedOrderZaloSms;
use App\Models\Business\DeliveryTimeline;
use App\Models\Business\Order;
use App\Models\Business\OrderCustomerReview;
use App\Models\Business\OrderDelivery;
use App\Models\Business\PublicHoliday;
use App\Models\Master\Customer;
use App\Models\Master\CustomerPhone;
use App\Models\Master\DistributionChannel;
use App\Models\Master\Image;
use App\Models\Master\OrderStatus;
use App\Models\Master\SaleDistrict;
use App\Models\Master\SaleGroup;
use App\Models\Master\Warehouse;
use App\Repositories\Abstracts\RepositoryAbs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
                '*.sap_po_number' => 'nullable|string|max:50',
                '*.sap_do_number' => 'nullable|string|max:20',
                '*.approveds.sap_so_finance_approval_date' => 'nullable|date',
                '*.details.delivery_address' => 'nullable|string|max:255',
                '*.details.note' => 'nullable|string',
                '*.details.total_item' => 'required|numeric',
                '*.details.total_weight' => 'required|numeric',
                '*.details.total_value' => 'required|numeric',
                '*.receivers.receiver_name' => 'required|string|max:255',
                '*.receivers.receiver_phone' => 'nullable|string|max:255',
                '*.sales.distribution_channel' => 'required|numeric|exists:distribution_channels,code',
                '*.sales.sale_group' => 'required|numeric|exists:sale_groups,code',
                '*.sales.sale_district' => 'required|numeric|exists:sale_districts,code',
                //'*.warehouse_code' => 'required|string|exists:warehouses,code',
                '*.is_deleted' => 'nullable|boolean'
            ], [
                '*.company_code.required' => 'Mã công ty là bắt buộc.',
                '*.company_code.exists' => 'Mã công ty :input không tồn tại.',
                '*.customer_code.required' => 'Mã khách hàng là bắt buộc.',
                '*.customer_code.exists' => 'Mã khách hàng :input không tồn tại.',
                '*.sap_so_number.required' => 'Số đơn hàng SAP là bắt buộc.',
                '*.sap_so_number.max' => 'Số đơn hàng SAP không được vượt quá 20 ký tự.',
                '*.sap_so_created_date.required' => 'Ngày tạo đơn hàng SAP là bắt buộc.',
                '*.sap_so_created_date.date' => 'Ngày tạo đơn hàng SAP :input không hợp lệ.',
                '*.sap_po_number.max' => 'Số đơn hàng PO không được vượt quá 50 ký tự.',
                '*.sap_do_number.max' => 'Số đơn hàng DO không được vượt quá 20 ký tự.',
                '*.approveds.sap_so_finance_approval_date.date' => 'Ngày phê duyệt tài chính SAP SO :input không hợp lệ.',
                '*.details.delivery_address.max' => 'Địa chỉ giao hàng không được vượt quá 255 ký tự.',
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
                '*.warehouse_code.exists' => 'Mã kho :input không tồn tại.',
                '*.sales.distribution_channel.exists' => 'Mã kênh phân phối :input không tồn tại.',
                '*.sales.sale_group.exists' => 'Mã nhóm bán hàng :input không tồn tại.',
                '*.sales.sale_district.exists' => 'Mã khu vực bán hàng :input không tồn tại.',
                '*.is_deleted.boolean' => 'Trạng thái xóa phải là true hoặc false.'
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $result = array(
                    'insert_list' => [],
                    'update_list' => [],
                    'skip_list' => [],
                    'delete_list' => [],
                    'error_list' => [],
                );

                DB::beginTransaction();
                foreach ($this->data as $order) {
                    if ($order['is_deleted']) {
                        $order = Order::where('sap_so_number', $order['sap_so_number'])->first();
                        if ($order) {
                            $order->delete();
                            $result['delete_list'][] = $order['sap_so_number'];
                        } else {
                            $result['skip_list'][] = $order['sap_so_number'];
                        }
                    } else {
                        $customer = Customer::where('code', $order['customer_code'])->first();
                        if (!$customer) {
                            $result['error_list'][] = $order['sap_so_number'];
                            $this->errors[] = 'Không tìm thấy khách hàng có mã ' . $order['customer_code'];
                            continue;
                        }
                        $warehouse = Warehouse::where('code', $order['warehouse_code'])->first();
                        if (!$warehouse) {
                            $result['error_list'][] = $order['sap_so_number'];
                            $this->errors[] = 'Không tìm thấy kho có mã ' . $order['warehouse_code'];
                            continue;
                        }
                        $existing_order = Order::where('sap_so_number', $order['sap_so_number'])->first();
                        if ($existing_order && $existing_order->status_id > EnumsOrderStatus::Pending) {
                            $result['skip_list'][] = $order['sap_so_number'];
                            continue;
                        }

                        $data = [
                            'company_code' => $order['company_code'],
                            'customer_id' => $customer->id,
                            'sap_so_created_date' => $order['sap_so_created_date'],
                            'sap_po_number' => $order['sap_po_number'] ?? '',
                            'sap_do_number' => $order['sap_do_number'] ?? '',
                            'status_id' => EnumsOrderStatus::Pending,
                            'warehouse_id' => $warehouse->id,
                            'is_draft' => $order['is_draft'] ?? false,
                        ];
                        if (!Order::where('sap_so_number', $order['sap_so_number'])->exists()) {
                            $data['id'] =  Str::uuid()->toString();
                        }
                        $created_order = Order::updateOrCreate(
                            [
                                'sap_so_number' => $order['sap_so_number']
                            ],
                            $data
                        );
                        // Check if order is approved by finance
                        if ($order['approveds']['sap_so_finance_approval_date'] && !$created_order->approved exists() || $created_order->approved()->exists() && $created_order->approved->sap_so_finance_approval_date != $order['approveds']['sap_so_finance_approval_date']) {
                            SendPreparedOrderZaloSms::dispatch($customer->id, $created_order->id);
                        }

                        $created_order->approved()->updateOrCreate(['order_id' => $created_order['id']], [
                            'sap_so_finance_approval_date' => $order['approveds']['sap_so_finance_approval_date'] ?? null,
                            'sap_do_posting_date' => $order['approveds']['sap_do_posting_date'      ] ?? null,
                        ]);
                        $created_order->detail()->updateOrCreate(['order_id' => $created_order['id']], [
                               'delivery_address' => $order['details']['delivery_address'] ?? '',
                            'note' => $order['details']['note'] ?? '',
                            'total_item' => $order['details']['total_item'],
                            'total_weight' => $order['details']['total_weight'],
                            'total_value' => $order['details']['total_value'],
                        ]);
                        $created_order->receiver()->updateOrCreate(['order_id' => $created_order['id']], [
                            'receiver_name' => $order['receivers']['receiver_name'],
                            'receiver_phone' => $order['receivers']['receiver_phone'] ?? '',
                            'note' => $order['receivers']['note'] ?? '',
                        ]);
                        $created_order->sale()->updateOrCreate(['order_id' => $created_order['id']], [
                            'distribution_channel_id' => DistributionChannel::where('code', $order['sales']['distribution_channel'])->first()->id,
                            'sale_district_id' => SaleDistrict::where('code', $order['sales']['sale_district'])->first()->id,
                            'sale_group_id' => SaleGroup::where('code', $order['sales']['sale_group'])->first()->id,
                        ]);

                        if ($created_order->wasRecentlyCreated || $created_order->getChanges()) {
                            $result['insert_list'][] = $order['sap_so_number'];
                        } else {
                            $result['update_list'][] = $order['sap_so_number'];
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

    public function getOrders($is_minified = false, $is_expanded = false)
    {
        try {
            if (!$this->current_user->hasRole(['admin-system', 'admin-warehouse', 'admin-partner'])) {
                return collect([]);
            }

            $query = Order::query();
            if ($this->request->filled('ids')) {
                $query->whereIn('id', $this->request->ids);
            }
            if ($this->request->filled('from_date')) {
                $from_date = $this->request->from_date;
                $query->whereDate('sap_so_created_date', '>=', $from_date);
            }
            if ($this->request->filled('to_date')) {
                $to_date = $this->request->to_date;
                $query->whereDate('sap_so_created_date', '<=', $to_date);
            }
            if ($this->request->filled('status_ids')) {
                $query->whereIn('status_id', $this->request->status_ids);
            }
            if ($this->request->filled('customer_id')) {
                $query->where('customer_id', $this->request->customer_id);
            }
            if ($this->request->filled('customer_ids')) {
                $query->whereIn('customer_id', $this->request->customer_ids);
            }
            if ($this->request->filled('warehouse_ids')) {
                $query->whereIn('warehouse_id', $this->request->warehouse_ids);
            }
            if ($this->request->filled('sap_so_number')) {
                $query->where('sap_so_number', 'LIKE', '%' . $this->request->sap_so_number . '%');
            }
            if ($this->request->filled('sap_do_number')) {
                $query->where('sap_do_number', 'LIKE', '%' . $this->request->sap_do_number . '%');
            }
            if ($this->request->filled('delivery_partner_ids')) {
                $delivery_partner_ids = $this->request->delivery_partner_ids;
                $query->whereHas('deliveries', function ($query) use ($delivery_partner_ids) {
                    $query->whereIn('delivery_partner_id', $delivery_partner_ids);
                });
            }
            if ($this->request->filled('distribution_channel_ids')) {
                $distribution_channel_ids = $this->request->distribution_channel_ids;
                $query->whereHas('sale', function ($query) use ($distribution_channel_ids) {
                    $query->whereIn('distribution_channel_id', $distribution_channel_ids);
                });
            }
            if ($this->request->filled('order_review_option_ids')) {
                $order_review_option_ids = $this->request->order_review_option_ids;
                $query->whereHas('customer_reviews', function ($query) use ($order_review_option_ids) {
                    $query->whereHas('criterias', function ($query) use ($order_review_option_ids) {
                        $query->whereIn('order_review_options.id', $order_review_option_ids);
                    });
                });
            }

            if ($this->current_user->hasRole('admin-partner')) {
                $channel_ids = $this->current_user->delivery_partners->flatMap(function ($partner) {
                    return $partner->distribution_channels;
                })->pluck('id')->toArray();

                $query->whereHas('sale', function ($q) use ($channel_ids) {
                    $q->whereIn('distribution_channel_id', $channel_ids);
                });
            }

            if ($this->request->filled('criteria_ids')) {
                $criteria_ids = $this->request->criteria_ids;
                if ($this->request->filled('month_year')) {
                    $month_year = $this->request->month_year;
                    list($month, $year) = explode('-', $month_year);
                    $query->leftJoin('order_approveds', 'order_approveds.order_id', '=', 'orders.id');
                    $query->select('orders.*');
                    $query->whereMonth('order_approveds.sap_so_finance_approval_date', $month)
                        ->whereYear('order_approveds.sap_so_finance_approval_date', $year);
                }
                $query->whereHas('customer_reviews', function ($q) use ($criteria_ids) {
                    $q->whereHas('criterias', function ($q) use ($criteria_ids) {
                        $q->whereIn('order_review_options.id', $criteria_ids);
                    });
                });
            }

            if ($is_minified) {
                $orders = $query->select(['orders.id', 'sap_so_number', 'sap_do_number'])->get();
            } elseif ($is_expanded) {
                $query->with(['company', 'customer', 'warehouse', 'delivery_info', 'detail', 'receiver', 'delivery_info.delivery.timelines', 'delivery_info.delivery.partner', 'approved', 'sale.distribution_channel', 'status', 'customer_reviews', 'customer_reviews.criterias', 'customer_reviews.user', 'customer_reviews.images']);
                $orders = $query->get();
                $public_holidays = PublicHoliday::all();
                $orders->map(function ($order) use ($public_holidays) {
                    if (isset($order->delivery_info)) {
                        $delivery = $order->delivery_info->delivery;
                    } else {
                        $delivery = null;
                    }
                    if (!$delivery) {
                        $order->duration = 0;
                        return $order;
                    }
                    $start_date = Carbon::parse($delivery->start_delivery_date)->setTimezone('Asia/Ho_Chi_Minh');
                    if (!$delivery->estimate_delivery_date) {
                        $order->duration = 0;
                        return $order;
                    }
                    $estimate_date = Carbon::parse($delivery->estimate_delivery_date)->setTimezone('Asia/Ho_Chi_Minh');
                    $duration = $estimate_date->diffInDays($start_date) + 1;
                    //loop foreach between start_date and estimate_date
                    $looped_duration = $duration;
                    for ($i = 0; $i <= $looped_duration; $i++) {
                        $date = $start_date->copy()->addDays($i);
                        //check if date is public holiday
                        $is_holiday = false;
                        foreach ($public_holidays as $public_holiday) {
                            $start_holiday_date = Carbon::parse($public_holiday->start_holiday_date)->setTimezone('Asia/Ho_Chi_Minh');
                            $end_holiday_date = Carbon::parse($public_holiday->end_holiday_date)->setTimezone('Asia/Ho_Chi_Minh');
                            if ($date->between($start_holiday_date, $end_holiday_date)) {
                                $duration--;
                                $is_holiday = true;
                            }
                        }
                        if (!$is_holiday && $date->format('l') == 'Sunday') {
                            $duration--;
                        }
                    }
                    $order->duration = $duration;
                    if ($delivery->complete_delivery_date) {
                        $delivery['status'] = EnumsOrderStatus::Delivered;
                    } else if ($delivery->start_delivery_date) {
                        $delivery['status'] = EnumsOrderStatus::Delivering;

                        // Check if any order is delivered or partly delivered

                    } else {
                        $delivery['status'] = EnumsOrderStatus::Preparing;
                    }

                    if ($delivery['status'] >= EnumsOrderStatus::Preparing && $delivery->estimate_delivery_date) {

                        if ($delivery['status'] >= EnumsOrderStatus::Delivered) {
                            if ($delivery->estimate_delivery_date->lt($delivery->complete_delivery_date)) {
                                $delivery['is_late_deadline'] = false;
                            } else {
                                $delivery['is_late_deadline'] = true;
                            }
                        } else {
                            if ($delivery->estimate_delivery_date->lt(Carbon::today())) {
                                $delivery['is_late_deadline'] = false;
                            } else {
                                $delivery['is_late_deadline'] = true;
                            }
                        }
                    } else {
                        $delivery['is_late_deadline'] = false;
                    }

                    $delivery['status'] = OrderStatus::find($delivery['status']);
                    return $delivery;
                });
            } else {
                $orders = $query
                    ->with(['company', 'customer', 'warehouse', 'detail', 'receiver', 'delivery_info', 'delivery_info.delivery.timelines', 'approved', 'sale', 'status', 'customer_reviews', 'customer_reviews.criterias', 'customer_reviews.user', 'customer_reviews.images'])
                    ->get();
            }

            return $orders;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function getOrdersByCustomer()
    {
        try {
            $query = Order::query();
            if ($this->request->filled('from_date')) {
                $query->whereDate('sap_so_created_date', '>=', $this->request->from_date);
            }
            if ($this->request->filled('to_date')) {
                $query->whereDate('sap_so_created_date', '<=', $this->request->to_date);
            }
            if ($this->request->filled('status')) {
                if ($this->request->status == 'undone') {
                    $query->where('status_id', '<', EnumsOrderStatus::Delivered);
                } else if ($this->request->status == 'delivering') {
                    $query->whereIn('status_id', [EnumsOrderStatus::Delivering, EnumsOrderStatus::PartlyDelivered]);
                }
            }
            if ($this->request->filled('search')) {
                $query->where('sap_so_number', $this->request->search)->orWhere('sap_so_number', 'like', '%' . $this->request->search . '%');
            }

            $customer_phones = CustomerPhone::select('customer_id')->where('phone_number', $this->current_user->phone_number)->get()->pluck('customer_id');
            if ($customer_phones) {
                $query->whereIn('customer_id', $customer_phones);

                $query = $query->with(['company', 'customer', 'warehouse', 'detail', 'receiver', 'approved', 'sale', 'status', 'delivery_info', 'customer_reviews', 'customer_reviews.criterias', 'customer_reviews.user', 'customer_reviews.images']);
                if ($this->request->filled('limit')) {
                    $query = $query->limit($this->request->limit);
                }

                $orders = $query->get();
                return $orders;
            } else {
                $this->message = 'Không tìm thấy khách hàng có số điện thoại ' . $this->current_user->phone_number;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function getOrderById($order_id, $is_expanded = false)
    {
        try {

            $order = Order::find($order_id);
            if ($order) {
                // $order->load(['company', 'customer', 'warehouse', 'detail', 'receiver', 'approved', 'sale', 'status', 'customer_reviews']);
                if ($is_expanded) {
                    $order->load(['company', 'customer', 'warehouse', 'detail', 'receiver', 'delivery_info', 'delivery_info.delivery.timelines', 'approved', 'sale', 'status', 'customer_reviews', 'customer_reviews.criterias', 'customer_reviews.user', 'customer_reviews.images']);
                } else {
                    $order->load(['company', 'customer', 'warehouse', 'detail', 'receiver', 'approved', 'sale', 'status', 'customer_reviews']);
                }
                return $order;
            } else {
                $this->message = 'Không tìm thấy đơn hàng có id ' . $order_id;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function confirmOrder($order_id)
    {
        try {
            $order = Order::find($order_id);
            if (!$order) {
                $this->message = 'Đơn hàng không tồn tại.';
                return false;
            }
            if ($order->status_id != EnumsOrderStatus::Delivered) {
                $this->message = 'Đơn hàng ' . $order->sap_so_number . ' chưa được giao, không thể xác nhận.';
                return false;
            }
            $customer_ids = CustomerPhone::where('phone_number', $this->current_user->phone_number)->get()->pluck('customer_id')->toArray();
            if (!$customer_ids || !in_array($order->customer_id, $customer_ids)) {
                $this->message = 'Bạn không có quyền xác nhận đơn hàng này.';
                return false;
            }

            DB::beginTransaction();
            $order->update(['status_id' => EnumsOrderStatus::Received]);
            OrderDelivery::where('order_id', $order->id)
                ->update([
                    'confirm_delivery_date' => now(),
                    'confirm_user_id' => $this->current_user->id
                ]);
            DB::commit();

            return true;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function confirmMultipleOrders()
    {
        try {
            $order_ids = $this->data['order_ids'];
            $orders = Order::whereIn('id', $order_ids)->get();
            if (count($orders) != count($order_ids)) {
                $this->message = 'Có đơn hàng không tồn tại.';
                return false;
            }
            foreach ($orders as $order) {
                if ($order->status_id != EnumsOrderStatus::Delivered) {
                    $this->message = 'Đơn hàng ' . $order->sap_so_number . ' chưa được giao, không thể xác nhận.';
                    return false;
                }
                $customer_ids = CustomerPhone::where('phone_number', $this->current_user->phone_number)->get()->pluck('customer_id')->toArray();
                if (!$customer_ids || !in_array($order->customer_id, $customer_ids)) {
                    $this->message = 'Bạn không có quyền xác nhận đơn hàng này.';
                    return false;
                }
            }

            DB::beginTransaction();
            foreach ($orders as $order) {
                $order->update(['status_id' => EnumsOrderStatus::Received]);
                OrderDelivery::where('order_id', $order->id)
                    ->update([
                        'confirm_delivery_date' => now(),
                        'confirm_user_id' => $this->current_user->id
                    ]);
                DB::commit();
            }
            return true;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function reviewOrder($order_id)
    {
        try {
            $validator = Validator::make($this->data, [
                'reviews' => 'required|array',
                'reviews.*' => 'required|exists:order_review_options,id',
                'note' => 'nullable|string|max:120',
                'images' => 'nullable|array',
            ], [
                'reviews.required' => 'Danh sách đánh giá là bắt buộc.',
                'reviews.array' => 'Danh sách đánh giá phải là một mảng.',
                'reviews.*.required' => 'Đánh giá là bắt buộc.',
                'reviews.*.exists' => 'Đánh giá :input không tồn tại.',
                'note.max' => 'Ghi chú không được vượt quá 120 ký tự.',
                'images.array' => 'Danh sách hình ảnh phải là một mảng.',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $order = Order::find($order_id);
                if (!$order) {
                    $this->message = 'Đơn hàng không tồn tại.';
                    return false;
                }
                if ($order->status_id != EnumsOrderStatus::Received) {
                    $this->message = 'Đơn hàng ' . $order->sap_so_number . ' chưa được nhận, không thể đánh giá.';
                    return false;
                }
                $customer_ids = CustomerPhone::where('phone_number', $this->current_user->phone_number)->get()->pluck('customer_id')->toArray();
                if (!$customer_ids || !in_array($order->customer_id, $customer_ids)) {
                    $this->message = 'Bạn không có quyền đánh giá đơn hàng này.';
                    return false;
                }

                DB::beginTransaction();
                $review = $order->customer_reviews()->create([
                    'review_content' => $this->data['note'] ?? '',
                    'user_id' => $this->current_user->id
                ]);
                $review->criterias()->sync($this->data['reviews']);
                $this->storeReviewImages($review, $this->data['images'] ?? []);
                DB::commit();

                return true;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    private function storeReviewImages($review, $upload_images)
    {
        foreach ($upload_images as $upload_image) {
            $name = uniqid();

            $image_properties = getimagesize($upload_image);
            $width = $image_properties[0];
            $height = $image_properties[1];
            $size = $upload_image->getSize();

            $image = new Image();
            $image->name = $name;
            $image->owner_id = $this->current_user->id;
            $image->ext = $upload_image->getClientOriginalExtension();
            $image->width = $width;
            $image->height = $height;
            $image->size = $size;
            $image->url = 'images/' . $name; // Adjust the URL as needed for your application
            $random_string = Str::random(10);
            $file_name = $image->name . '_' . $random_string . '.' . $image->ext;
            $image->url = 'images/' . $file_name;

            Storage::disk('images')->put($file_name, file_get_contents($upload_image));

            $review->images()->save($image, ['imageable_id' => $review->id, 'imageable_type' => OrderCustomerReview::class]);
        }
    }
}
