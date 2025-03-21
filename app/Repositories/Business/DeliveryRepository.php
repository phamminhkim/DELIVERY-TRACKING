<?php

namespace App\Repositories\Business;

use App\Enums\ApiRequestMethod;
use App\Enums\OrderStatus as EnumsOrderStatus;
use App\Jobs\SendFinishedDeliveryZaloSms;
use App\Jobs\SendStartedDeliveryZaloSms;
use App\Jobs\SendZaloSms;
use App\Models\Business\Delivery;
use App\Models\Business\DeliveryToken;
use App\Models\Business\DeliveryTokenScan;
use App\Models\Business\Order;
use App\Models\Business\OrderDelivery;
use App\Models\Business\OrderDriverConfirm;
use App\Models\Business\PrintConfig;
use App\Models\Master\Customer;
use App\Models\Master\Warehouse;
use App\Models\Master\DistributionChannel;
use App\Models\Master\CustomerPhone;
use App\Models\Master\DeliveryPartner;
use App\Models\Master\ExternalDeliveryCode;
use App\Models\Master\Image;
use App\Models\Master\OrderStatus;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Implementations\Files\LocalFileService;
use App\Utilities\ApiRequest;
use App\Utilities\QrCodeUtility;
use App\Utilities\UniqueIdUtility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\Excel\ExcelExtractor;
use App\Utilities\ApiHandler;
use App\Utilities\ApiUtility;

class DeliveryRepository extends RepositoryAbs
{
    public function getDeliveries()
    {
        try {
            if (!$this->current_user->hasRole(['admin-system', 'admin-warehouse', 'admin-partner'])) {
                return collect([]);
            }

            $query = Delivery::query();
            if ($this->request->filled('filter')) {
                if ($this->request->filter == 'undone') {
                    $query->whereNull('complete_delivery_date');
                } else if ($this->request->filter == 'delivering') {
                    $query->whereNotNull('start_delivery_date')->whereNull('complete_delivery_date');
                }
            }
            if ($this->request->filled('ids')) {
                $query->whereIn('id', $this->request->ids);
            }
            if ($this->request->filled('from_date')) {
                $from_date = $this->request->from_date;
                $query->whereDate('created_at', '>=', $from_date);
            }
            if ($this->request->filled('to_date')) {
                $to_date = $this->request->to_date;
                $query->whereDate('created_at',  '<=', $to_date);
            }
            if ($this->request->filled('customer_id')) {
                $query->where('customer_id', $this->request->customer_id);
            }
            if ($this->request->filled('customer_ids')) {
                $query->whereIn('customer_id', $this->request->customer_ids);
            }
            if ($this->request->filled('sap_so_number')) {
                $sap_so_number = $this->request->sap_so_number;
                $query->whereHas('orders', function ($subQuery) use ($sap_so_number) {
                    $subQuery->where('sap_so_number', 'LIKE', '%' . $sap_so_number . '%');
                });
            }
            if ($this->request->filled('sap_do_number')) {
                $sap_do_number = $this->request->sap_do_number;
                $query->whereHas('orders', function ($subQuery) use ($sap_do_number) {
                    $subQuery->where('sap_do_number', 'LIKE', '%' . $sap_do_number . '%');
                });
            }
            if ($this->request->filled('warehouse_ids')) {
                $warehouse_ids = $this->request->warehouse_ids;
                $query->whereHas('orders', function ($subQuery) use ($warehouse_ids) {
                    $subQuery->whereIn('warehouse_id', $warehouse_ids);
                });
            }
            if ($this->request->filled('distribution_channel_ids')) {
                $distribution_channel_ids = $this->request->distribution_channel_ids;
                $query->whereHas('orders', function ($subQuery) use ($distribution_channel_ids) {
                    $subQuery->whereHas('sale', function ($query) use ($distribution_channel_ids) {
                        $query->whereIn('distribution_channel_id', $distribution_channel_ids);
                    });
                });
            }
            if ($this->request->filled('order_review_option_ids')) {
                $order_review_option_ids = $this->request->order_review_option_ids;
                $query->whereHas('orders', function ($subQuery) use ($order_review_option_ids) {
                    $subQuery->whereHas('customer_reviews', function ($query) use ($order_review_option_ids) {
                        $query->whereHas('criterias', function ($query) use ($order_review_option_ids) {
                            $query->whereIn('order_review_options.id', $order_review_option_ids);
                        });
                    });
                });
            }

            if ($this->request->filled('delivery_partner_ids')) {
                $query->whereIn('delivery_partner_id', $this->request->delivery_partner_ids);
            }

            if ($this->current_user->hasRole('admin-partner')) {
                $delivery_partner_ids = $this->current_user->delivery_partners->pluck('id')->toArray();

                $query->whereIn('delivery_partner_id', $delivery_partner_ids);
            }

            $deliveries = $query->with(['company', 'customer', 'partner', 'pickup', 'orders'])
                ->orderByRaw('CASE WHEN estimate_delivery_date IS NULL THEN 1 ELSE 0 END, DATEDIFF(estimate_delivery_date, CURDATE()) ASC')
                ->get();
            foreach ($deliveries as $delivery) {
                if ($delivery->complete_delivery_date) {
                    $delivery['status'] = EnumsOrderStatus::Delivered;
                } else if ($delivery->start_delivery_date) {
                    $delivery['status'] = EnumsOrderStatus::Delivering;

                    // Check if any order is delivered or partly delivered

                } else {
                    $delivery['status'] = EnumsOrderStatus::Preparing;
                }

                if ($delivery['status'] >= EnumsOrderStatus::Preparing && $delivery['status'] < EnumsOrderStatus::Delivered && $delivery->estimate_delivery_date) {
                    if ($delivery->estimate_delivery_date->isToday()) {
                        $delivery['is_near_deadline'] = true;
                    } else if ($delivery->estimate_delivery_date->lt(Carbon::today())) {
                        $delivery['is_late_deadline'] = true;
                    }
                }

                $delivery['status'] = OrderStatus::find($delivery['status']);
            }

            if ($this->request->filled('status_ids')) {
                $status_ids = $this->request->status_ids;
                $deliveries = $deliveries->filter(function ($delivery) use ($status_ids) {
                    return in_array($delivery->status->id, $status_ids);
                })->values();
            }
            return $deliveries;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getDeliveryByQrScan($qr_token, $is_customer)
    {
        try {
            $delivery_token = DeliveryToken::where('token', $qr_token)->first();
            if (!$delivery_token) {
                $scan_log['result'] = 'Mã QR không hợp lệ.';

                DeliveryTokenScan::create([
                    'token_id' => null,
                    'scan_by' => $this->current_user->id,
                    'scan_at' => now(),
                    'is_success' => false,
                    'result' => 'Invalid QR code'
                ]);
                $this->message = 'Mã QR không hợp lệ.';
                return false;
            } else {
                $delivery_token->scans()->create([
                    'scan_by' => $this->current_user->id,
                    'scan_at' => now(),
                    'is_success' => true,
                    'result' => 'Fetch delivery with id ' . strval($delivery_token->delivery_id)
                ]);

                return $this->getDeliveryById($delivery_token->delivery_id, $is_customer);
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();

            DeliveryTokenScan::create([
                'token_id' => $delivery_token ? $delivery_token->id : null,
                'scan_by' => $this->current_user->id,
                'scan_at' => now(),
                'is_success' => false,
                'result' => $exception->getMessage()
            ]);
        }
    }
    public function getDeliveryById($delivery_id, $is_customer)
    {
        try {
            $delivery = Delivery::find($delivery_id);
            if (!$delivery) {
                $this->message = 'Đơn vận chuyển không tồn tại.';
                return false;
            }
            foreach ($delivery->orders as $order) {
                $order->unsetRelation('pivot');
            }
            if ($is_customer) {
                $customer_phones = CustomerPhone::select('customer_id')->where('phone_number', $this->current_user->phone_number)->get()->pluck('customer_id');
                if ($customer_phones) {
                    $my_orders = $delivery->orders->whereIn('customer_id', $customer_phones);
                    $my_orders->load('status', 'detail', 'receiver', 'driver_confirms', 'driver_confirms.images', 'customer_reviews', 'customer_reviews.criterias', 'customer_reviews.images');
                    $my_orders = $my_orders->map(function ($order) {
                        $orders = $order->toArray();
                        return $orders;
                    })->values()->toArray();
                    if ($delivery->orders->count() == 0) {
                        $this->message = 'Không tìm thấy đơn hàng nào của khách hàng có số điện thoại ' . $this->current_user->phone_number;
                        return false;
                    }
                    $delivery->load(['company', 'customer', 'partner', 'info', 'timelines']);
                    unset($delivery->orders);
                    $delivery->orders = $my_orders;
                } else {
                    $this->message = 'Không tìm thấy khách hàng có số điện thoại ' . $this->current_user->phone_number;
                    return false;
                }
            } else {
                $delivery->load(['company', 'customer', 'partner', 'info', 'timelines', 'orders', 'orders.delivery_info', 'orders.status', 'orders.detail', 'orders.receiver', 'orders.driver_confirms', 'orders.driver_confirms.images',]);
                $my_orders = $delivery->orders->map(function ($order) {
                    $orders = $order->toArray();
                    return $orders;
                })->values()->toArray();
                unset($delivery->orders);
                $delivery->orders = $my_orders;
            }

            if ($delivery->info->confirm_delivery_date) {
                $delivery['status'] = EnumsOrderStatus::Received;
            } else if ($delivery->complete_delivery_date) {
                $delivery['status'] = EnumsOrderStatus::Delivered;
            } else if ($delivery->start_delivery_date) {
                $delivery['status'] = EnumsOrderStatus::Delivering;
                // Check if any order is delivered or partly delivered
            } else {
                $delivery['status'] = EnumsOrderStatus::Preparing;
            }
            $delivery['status'] = OrderStatus::find($delivery['status']);
            $delivery['can_single_confirm'] = true;
            return $delivery;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function getPrintConfigsOfUser()
    {
        $user_id = $this->current_user->id;
        $print_configs = PrintConfig::query()->where('user_id', '=', $user_id)->get();
        return $print_configs;
    }
    public function createPrintQRConfig()
    {
        try {
            $validator = Validator::make($this->data, [
                'name' => 'required|string',
                'config' => 'required|string',
            ], [
                'name.required' => 'Tên cấu hình là bắt buộc.',
                'config.required' => 'Cấu hình là bắt buộc.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $print_config = PrintConfig::create([
                    'name' => $this->data['name'],
                    'config' => $this->data['config'],
                    'user_id' => $this->current_user->id
                ]);
                DB::commit();

                return $print_config;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function deletePrintQRConfig($print_config_id)
    {
        try {
            $print_config = PrintConfig::find($print_config_id);
            if (!$print_config) {
                $this->message = 'Cấu hình không tồn tại.';
                return false;
            }
            DB::beginTransaction();
            $print_config->delete();
            DB::commit();

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function printDeliveryQrCodeById($delivery_id)
    {
        try {
            $delivery = Delivery::find($delivery_id);
            if (!$delivery) {
                $this->message = 'Đơn vận chuyển không tồn tại.';
                return false;
            }
            $token = $delivery->primary_token;
            if (!$token) {
                $this->message = 'Mã Qr không tồn tại.';
                return false;
            }

            $qr_code = QrCodeUtility::generateImage($token->token);
            return strval($qr_code);
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function printDeliveriesQrCodeByIds()
    {
        try {
            $delivery_ids = $this->data['delivery_ids'];
            $print_config = $this->data['print_config'];
            $query = Delivery::query();
            $deliveies = $query->whereIn('id', $delivery_ids)->with(['orders'])->get();
            $qr_datas = [];
            foreach ($deliveies as $delivery) {
                $token = $delivery->primary_token;
                $converting_url = sprintf('%s/scan-qr/%s', config('app.url'), $token->token);
                $qr_code = QrCodeUtility::generateImage($converting_url);
                $qr_code_xml = strval($qr_code);
                $delivery_code = $delivery->delivery_code;
                foreach ($delivery->orders as $order) {
                    $qr_datas[] = [
                        'delivery_code' => $delivery_code,
                        'sap_do_number' => $order->sap_do_number,
                        'qr_code' => $qr_code_xml
                    ];
                }
            }
            sort($delivery_ids);
            $hashed_ids = hash("sha256", serialize([serialize($delivery_ids), serialize($print_config)]));
            $html_content = view('app.print_qr_code', compact('qr_datas', 'print_config'))->render();
            $directory = public_path('print_qr');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            $path = sprintf("%s/%s.html", $directory, $hashed_ids);
            if (File::exists($path)) {
                return asset(sprintf("print_qr/%s.html", $hashed_ids));
            }
            File::put($path, $html_content);
            return asset(sprintf("print_qr/%s.html", $hashed_ids));
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function confirmPickupDelivery($delivery_id)
    {
        try {
            $validator = Validator::make($this->data, [
                'driver_name' => 'required|string|max:50',
                'driver_note' => 'nullable|string|max:120',
                'driver_plate_number' => 'required|string|max:20',
            ], [
                'driver_name.required' => 'Tên tài xế là bắt buộc.',
                'driver_name.string' => 'Tên tài xế không đúng định dạng.',
                'driver_name.max' => 'Tên tài xế không được vượt quá 50 ký tự.',
                'driver_note.string' => 'Ghi chú tài xế không đúng định dạng.',
                'driver_note.max' => 'Ghi chú tài xế không được vượt quá 120 ký tự.',
                'driver_plate_number.required' => 'Biển số xe là bắt buộc.',
                'driver_plate_number.string' => 'Biển số xe không đúng định dạng.',
                'driver_plate_number.max' => 'Biển số xe không được vượt quá 20 ký tự.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $delivery = Delivery::find($delivery_id);
                if (!$delivery) {
                    $this->message = 'Đơn vận chuyển không tồn tại.';
                    return false;
                }

                DB::beginTransaction();
                $delivery->pickup()->create([
                    'pickup_at' => now(),
                    'driver_phone' => $this->current_user->phone_number,
                    'driver_name' => $this->data['driver_name'],
                    'driver_note' => $this->data['driver_note'] ?? '',
                    'driver_plate_number' => $this->data['driver_plate_number'],
                ]);
                $delivery->update(['start_delivery_date' => now()]);

                $delivery->orders->each(function ($order) use ($delivery) {
                    $order->update(['status_id' => EnumsOrderStatus::Delivering]);
                    OrderDelivery::where('order_id', $order->id)
                        ->where('delivery_id', $delivery->id)
                        ->update(['start_delivery_date' => now()]);
                });
                $delivery->timelines()->create([
                    'event' => 'confirm_pickup_delivery',
                    'description' => 'Tài xế ' . $this->data['driver_name'] . ' đã nhận vận đơn & bắt đầu giao hàng.',
                ]);
                DB::commit();

                return true;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function confirmOrderDelivery($delivery_id, $order_id)
    {
        try {
            $validator = Validator::make($this->data, [
                'confirm_status' => 'required|in:fully,partly',
                'driver_name' => 'required|string|max:50',
                'driver_note' => 'nullable|string|max:120',
                'driver_plate_number' => 'required|string|max:20',
                'images' => 'nullable|array',
            ], [
                'confirm_status.required' => 'Trạng thái xác nhận là bắt buộc.',
                'confirm_status.in' => 'Trạng thái xác nhận không hợp lệ.',
                'driver_name.required' => 'Tên tài xế là bắt buộc.',
                'driver_name.string' => 'Tên tài xế không đúng định dạng.',
                'driver_name.max' => 'Tên tài xế không được vượt quá 50 ký tự.',
                'driver_note.string' => 'Ghi chú tài xế không đúng định dạng.',
                'driver_note.max' => 'Ghi chú tài xế không được vượt quá 120 ký tự.',
                'driver_plate_number.required' => 'Biển số xe là bắt buộc.',
                'driver_plate_number.string' => 'Biển số xe không đúng định dạng.',
                'driver_plate_number.max' => 'Biển số xe không được vượt quá 20 ký tự.',
                'images.array' => 'Danh sách hình ảnh không đúng định dạng.',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $delivery = Delivery::find($delivery_id);
                if (!$delivery) {
                    $this->message = 'Đơn vận chuyển không tồn tại.';
                    return false;
                }
                $order = Order::find($order_id);
                if (!$order) {
                    $this->message = 'Đơn hàng không tồn tại.';
                    return false;
                }
                $order_delivery = OrderDelivery::where('order_id', $order->id)
                    ->where('delivery_id', $delivery->id)
                    ->first();
                if (!$order_delivery) {
                    $this->message = 'Đơn hàng không thuộc đơn vận chuyển này.';
                    return false;
                }
                if ($order->status_id == EnumsOrderStatus::Delivered) {
                    $this->message = 'Đơn hàng ' . $order->sap_so_number . ' đã được giao.';
                    return false;
                }

                DB::beginTransaction();
                $confirm = $order->driver_confirms()->create([
                    'complete_delivery_date' => now(),
                    'confirm_status' => $this->data['confirm_status'],
                    'driver_phone' => $this->current_user->phone_number,
                    'driver_name' => $this->data['driver_name'],
                    'driver_note' => $this->data['driver_note'] ?? '',
                    'driver_plate_number' => $this->data['driver_plate_number'],
                ]);
                $this->storeConfirmImages($confirm, $this->data['images'] ?? []);
                if ($this->data['confirm_status'] == 'fully') {
                    $order->update(['status_id' => EnumsOrderStatus::Delivered]);
                    $order_delivery->update(['complete_delivery_date' => now()]);
                    $delivery->timelines()->create([
                        'event' => 'confirm_fully_order_delivery',
                        'description' => 'Hoàn tất giao đơn hàng ' . $order->sap_so_number . '.',
                    ]);
                    SendFinishedDeliveryZaloSms::dispatch($order->customer_id, $order->id);
                } else {
                    $order->update(['status_id' => EnumsOrderStatus::PartlyDelivered]);
                    $delivery->timelines()->create([
                        'event' => 'confirm_partly_order_delivery',
                        'description' => 'Giao một phần hàng trong đơn hàng ' . $order->sap_so_number . '.',
                    ]);
                }
                DB::commit();

                return $order;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function confirmDelivery($delivery_id)
    {
        try {
            $validator = Validator::make($this->data, [
                'driver_name' => 'required|string|max:50',
                'driver_note' => 'nullable|string|max:120',
                'driver_plate_number' => 'required|string|max:20',
                'images' => 'nullable|array',
            ], [
                'driver_name.required' => 'Tên tài xế là bắt buộc.',
                'driver_name.string' => 'Tên tài xế không đúng định dạng.',
                'driver_name.max' => 'Tên tài xế không được vượt quá 50 ký tự.',
                'driver_note.string' => 'Ghi chú tài xế không đúng định dạng.',
                'driver_note.max' => 'Ghi chú tài xế không được vượt quá 120 ký tự.',
                'driver_plate_number.required' => 'Biển số xe là bắt buộc.',
                'driver_plate_number.string' => 'Biển số xe không đúng định dạng.',
                'driver_plate_number.max' => 'Biển số xe không được vượt quá 20 ký tự.',
                'images.array' => 'Danh sách hình ảnh không đúng định dạng.',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $delivery = Delivery::find($delivery_id);
                if (!$delivery) {
                    $this->message = 'Đơn vận chuyển không tồn tại.';
                    return false;
                }
                $orders = $delivery->orders()->where('status_id', '<', EnumsOrderStatus::Delivered)->get();

                DB::beginTransaction();
                foreach ($orders as $order) {
                    $confirm = $order->driver_confirms()->create([
                        'complete_delivery_date' => now(),
                        'confirm_status' => 'fully',
                        'driver_phone' => $this->current_user->phone_number,
                        'driver_name' => $this->data['driver_name'],
                        'driver_note' => $this->data['driver_note'] ?? '',
                        'driver_plate_number' => $this->data['driver_plate_number'],
                    ]);
                    $this->storeConfirmImages($confirm, $this->data['images'] ?? []);
                    $order->update(['status_id' => EnumsOrderStatus::Delivered]);
                    $order->delivery_info->update(['complete_delivery_date' => now()]);
                    $delivery->timelines()->create([
                        'event' => 'confirm_fully_order_delivery',
                        'description' => 'Hoàn tất giao đơn hàng ' . $order->sap_so_number . '.',
                    ]);
                    SendFinishedDeliveryZaloSms::dispatch($order->customer_id, $order->id);
                }
                DB::commit();

                return $orders;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function completeDelivery($delivery_id)
    {
        try {
            $delivery = Delivery::find($delivery_id);
            if (!$delivery) {
                $this->message = 'Đơn vận chuyển không tồn tại.';
                return false;
            }
            if ($delivery->complete_delivery_date) {
                $this->message = 'Đơn vận chuyển đã được hoàn thành.';
                return false;
            }
            if (!$delivery->start_delivery_date) {
                $this->message = 'Đơn vận chuyển chưa được bắt đầu.';
                return false;
            }
            if ($delivery->orders->where('status_id', '!=', EnumsOrderStatus::Delivered)->count() > 0) {
                $this->message = 'Đơn vận chuyển có đơn hàng chưa hoàn thành.';
                return false;
            }
            DB::beginTransaction();
            $delivery->update(['complete_delivery_date' => now()]);
            $delivery->timelines()->create([
                'event' => 'complete_delivery',
                'description' => 'Đơn vận chuyển hoàn tất.',
            ]);
            DB::commit();

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    private function storeConfirmImages($confirm, $upload_images)
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

            $confirm->images()->save($image, ['imageable_id' => $confirm->id, 'imageable_type' => OrderDriverConfirm::class]);
        }
    }

    public function createDelivery()
    {
        try {
            $validator = Validator::make($this->data, [
                'company_code' => 'required|exists:companies,code',
                'delivery_partner_code' => 'required|string|exists:delivery_partners,code',
                'customer_id' => 'required|exists:customers,id',
                'orders' => 'required|array',
                'orders.*.id' => 'required|uuid|exists:orders',
            ], [
                'company_code.required' => 'Mã công ty là bắt buộc.',
                'company_code.exists' => 'Mã công ty không tồn tại.',
                'delivery_partner_code.required' => 'Mã đối tác giao hàng là bắt buộc.',
                'delivery_partner_code.exists' => 'Mã đối tác giao hàng không tồn tại.',
                'customer_id.required' => 'Id khách hàng là bắt buộc.',
                'customer_id.exists' => 'Id khách hàng không tồn tại.',
                'orders.required' => 'Danh sách đơn hàng là bắt buộc.',
                'orders.*.id.required' => 'Mã đơn hàng là bắt buộc.',
                'orders.*.id.uuid' => 'Mã đơn hàng không đúng định dạng.',
                'orders.*.id.exists' => 'Mã đơn hàng không tồn tại.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $delivery_partner = DeliveryPartner::where('code', $this->data['delivery_partner_code'])->first();
                $delivery_code = UniqueIdUtility::generateDeliveryUniqueCode($delivery_partner);
                $customer = Customer::find($this->data['customer_id']);

                $delivery = Delivery::create([
                    'delivery_code' => $delivery_code,
                    'company_code' => $this->data['company_code'],
                    'delivery_partner_id' => $delivery_partner->id,
                    'customer_id' => $customer->id,
                    'start_delivery_date' => null,
                    'complete_delivery_date' => null,
                    'estimate_delivery_date' => $this->data['estimate_delivery_date'],
                    'address' => $this->data['address'],
                    'created_by' => $this->current_user->id
                ]);

                $generated_token = $this->createDeliveryToken($delivery, $delivery_partner);
                if (!$generated_token) {
                    $this->message = 'Đơn vị vận chuyển không hỗ trợ tích hợp.';
                    return false;
                }

                foreach ($this->data['orders'] as $order) {
                    $order = Order::find($order['id']);
                    if ($order->status_id == EnumsOrderStatus::Preparing) {
                        $this->message = 'Đơn hàng ' . $order->sap_so_number . ' đang được xử lí.';
                        return false;
                    }
                    if ($order->status_id == EnumsOrderStatus::Delivering) {
                        $this->message = 'Đơn hàng ' . $order->sap_so_number . ' đang được giao.';
                        return false;
                    }
                    if ($order->status_id == EnumsOrderStatus::Delivered) {
                        $this->message = 'Đơn hàng ' . $order->sap_so_number . ' đã được giao.';
                        return false;
                    }

                    $order->status_id = EnumsOrderStatus::Preparing;
                    $order->save();

                    OrderDelivery::create([
                        'order_id' => $order->id,
                        'delivery_id' => $delivery->id,
                        'start_delivery_at' => null,
                        'complete_delivery_at' => null,
                        'estimate_delivery_at' => $delivery->estimate_delivery_date,
                        'address' => $delivery->address
                    ]);
                }
                $delivery->timelines()->create([
                    'event' => 'create_delivery',
                    'description' => 'Tạo & in đơn vận chuyển.',
                ]);

                $qr_code = QrCodeUtility::generateImage($generated_token->token);
                DB::commit();

                foreach ($delivery->orders as $order) {
                    SendStartedDeliveryZaloSms::dispatch($customer->id, $order->id, $delivery->estimate_delivery_date);
                }

                $result = array(
                    'delivery_id' => $delivery->id,
                    'qr_token' => strval($qr_code),
                    'total_orders' => count($this->data['orders'])
                );
                return $result;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    private function createDeliveryToken($delivery, $delivery_partner)
    {
        if ($delivery_partner->is_external) {
            $token = DeliveryToken::create([
                'delivery_id' => $delivery->id,
                'delivery_partner_id' => $delivery_partner->id,
                //'token' => '1234567890',
                'is_primary' => true,
            ]);
            return null;
        } else {
            $new_token =  Hash::make(Str::uuid()->toString());
            $token_str = str_replace([':', '/', '\\'], '', $new_token);

            $token = $delivery->tokens()->create([
                'delivery_id' => $delivery->id,
                'delivery_partner_id' => $delivery_partner->id,
                'token' => $token_str,
                'is_primary' => true,
            ]);
            return $token;
        }
    }

    public function updateDelivery($delivery_id)
    {
        try {
            $validator = Validator::make($this->data, [
                'orders' => 'required|array',
                'orders.*.id' => 'required|uuid|exists:orders',
                'estimate_delivery_date' => 'required|date_format:Y-m-d',
            ], [
                'orders.*.required' => 'Mã đơn hàng là bắt buộc.',
                'orders.*.uuid' => 'Mã đơn hàng không đúng định dạng.',
                'orders.*.exists' => 'Mã đơn hàng không tồn tại.',
                'estimate_delivery_date.required' => 'Ngày dự kiến giao là bắt buộc.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $delivery = Delivery::find($delivery_id);
                if (!$delivery) {
                    $this->message = 'Đơn vận chuyển không tồn tại.';
                    return false;
                }
                if ($delivery->complete_delivery_date) {
                    $this->message = 'Vận đơn đã hoàn thành, không thể chỉnh sửa.';
                    return false;
                }
                DB::beginTransaction();

                $new_order_ids = array_map(function ($item) {
                    return $item['id'];
                }, $this->request['orders']);
                $insert_list = array_diff($new_order_ids, $delivery->orders->pluck('id')->toArray());
                $delete_list = array_diff($delivery->orders->pluck('id')->toArray(), $new_order_ids);

                foreach ($insert_list as $order_id) {
                    $order = Order::find($order_id);
                    if ($order->status_id == EnumsOrderStatus::Preparing) {
                        $this->message = 'Đơn hàng ' . $order->sap_so_number . ' đang được xử lí.';
                        return false;
                    }
                    if ($order->status_id == EnumsOrderStatus::Delivering) {
                        $this->message = 'Đơn hàng ' . $order->sap_so_number . ' đang được giao.';
                        return false;
                    }
                    if ($order->status_id == EnumsOrderStatus::Delivered) {
                        $this->message = 'Đơn hàng ' . $order->sap_so_number . ' đã được giao.';
                        return false;
                    }

                    $order->status_id = EnumsOrderStatus::Preparing;
                    $order->save();

                    OrderDelivery::create([
                        'order_id' => $order->id,
                        'delivery_id' => $delivery->id,
                        'start_delivery_at' => null,
                        'complete_delivery_at' => null,
                    ]);
                };

                foreach ($delete_list as $order_id) {
                    $order = Order::find($order_id);
                    if ($order->status_id == EnumsOrderStatus::Delivering) {
                        $this->message = 'Đơn hàng ' . $order->sap_so_number . ' đang được giao.';
                        return false;
                    }
                    if ($order->status_id == EnumsOrderStatus::Delivered) {
                        $this->message = 'Đơn hàng ' . $order->sap_so_number . ' đã được giao.';
                        return false;
                    }

                    $order->status_id = EnumsOrderStatus::Pending;
                    $order->save();

                    OrderDelivery::where('order_id', $order->id)->where('delivery_id', $delivery->id)->delete();
                };
                $delivery->estimate_delivery_date = $this->data['estimate_delivery_date'];
                $delivery->save();

                DB::commit();

                $result = array(
                    'delivery_id' => $delivery->id,
                    'total_orders' => count($this->data['orders'])
                );
                return $result;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function deleteDelivery($delivery_id)
    {
        try {
            $delivery = Delivery::find($delivery_id);
            if (!$delivery) {
                $this->message = 'Đơn vận chuyển không tồn tại.';
                return false;
            }

            DB::beginTransaction();
            $delivery->tokens()->delete();
            $delivery->timelines()->delete();
            foreach ($delivery->orders as $order) {
                $order->status_id = EnumsOrderStatus::Pending;
                $order->save();
            }
            OrderDelivery::where('delivery_id', $delivery->id)->delete();
            $delivery->delete();
            DB::commit();

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function createExternalDeliveryFromExcel()
    {
        try {
            $validator = Validator::make($this->data, [
                'file' => 'required|mimes:xlsx,xls',
                'delivery_partner' => 'required|exists:delivery_partners,id',
                'company_code' => 'required|exists:companies,code',
            ], [
                'file.required' => 'File là bắt buộc.',
                'file.mimes' => 'File không đúng định dạng.',
                'delivery_partner.required' => 'Đối tác giao hàng là bắt buộc.',
                'delivery_partner.exists' => 'Đối tác giao hàng không tồn tại.',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $file = $this->request->file('file');
                $excel_extractor = new ExcelExtractor();
                $raw_table_data = $excel_extractor->extractData($file);
                $template_structure = [
                    "delivery_code_customer" => 0,
                    "sap_do_number" => 1,
                    "customer_code" => 2,
                    "estimate_delivery_date" => 4,
                ];
                $table_data = $excel_extractor->structureData($raw_table_data, $template_structure, 'delivery_code_customer');

                $delivery_partner = DeliveryPartner::find($this->data['delivery_partner']);
                if (!$delivery_partner->is_external) {
                    $this->message = 'Không phải đối tác giao hàng ngoài.';
                    return false;
                }
                $api_mapping = $delivery_partner->mapping;
                DB::beginTransaction();
                $delivieries_result = [];
                foreach ($table_data as $delivery_code_customer => $delivery_customer) {
                    $sap_do_numbers = array_map(function ($item) {
                        return $item['sap_do_number'];
                    }, $delivery_customer);
                    $orders = Order::whereIn('sap_do_number', $sap_do_numbers)->get();
                    if ($orders->count() != count($delivery_customer)) {
                        $not_exist_do = array_diff($sap_do_numbers, $orders->pluck('sap_do_number')->toArray());
                        $this->message = 'Có DO không tồn tại trong file. (' . implode(', ', $not_exist_do) . ')';
                        return false;
                    }
                    $customer = Customer::where('code', $delivery_customer[0]['customer_code'] ?? null)->first();
                    if (!$customer) {
                        $customer_not_exist = $delivery_customer[0]['customer_code'];
                        $this->message = "Khách hàng $customer_not_exist không tồn tại.";
                        return false;
                    }
                    $delivery_code = UniqueIdUtility::generateDeliveryUniqueCode($delivery_partner);
                    $body_datas = json_decode($delivery_partner->api_body_datas, true);
                    $body_datas = array_merge($body_datas, array($delivery_partner->api_delivery_code_field => $delivery_code_customer));
                    // [$api_customer_response] = ApiHandler::handleMultipleRequests([
                    //     new ApiRequest($delivery_partner->api_url, [], $body_datas, $delivery_partner->api_method),
                    // ]);

                    // $api_customer_response = ApiUtility::getPropertyByPath($api_customer_response, $api_mapping->root_data_field, '.');
                    // if ($api_mapping->is_root_string) {
                    //     $api_customer_response = json_decode($api_customer_response, true);
                    // }
                    // $api_customer_response = $api_customer_response[0]; //kết quả trả về thường là mảng
                    $delivery = Delivery::create([
                        'delivery_code' => $delivery_code,
                        'company_code' => $this->data['company_code'],
                        'delivery_partner_id' => $delivery_partner->id,
                        'customer_id' => $customer->id,
                        'start_delivery_date' => Carbon::now(),
                        'complete_delivery_date' => null,
                        'estimate_delivery_date' => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($delivery_customer[0]['estimate_delivery_date'])),
                        'address' => $customer->address,
                        'created_by' => $this->current_user->id
                    ]);
                    ExternalDeliveryCode::create([
                        'delivery_id' => $delivery->id,
                        'external_delivery_code' => $delivery_code_customer,
                    ]);

                    foreach ($orders as $order) {

                        if ($order->status_id == EnumsOrderStatus::Preparing) {
                            $this->message = 'Đơn hàng ' . $order->sap_so_number . ' đang được xử lí.';
                            return false;
                        }
                        if ($order->status_id == EnumsOrderStatus::Delivering) {
                            $this->message = 'Đơn hàng ' . $order->sap_so_number . ' đang được giao.';
                            return false;
                        }
                        if ($order->status_id == EnumsOrderStatus::Delivered) {
                            $this->message = 'Đơn hàng ' . $order->sap_so_number . ' đã được giao.';
                            return false;
                        }

                        $order->status_id = EnumsOrderStatus::Preparing;
                        $order->save();

                        OrderDelivery::create([
                            'order_id' => $order->id,
                            'delivery_id' => $delivery->id,
                            'start_delivery_at' => Carbon::now(),
                            'complete_delivery_at' => null,
                            'estimate_delivery_at' => $delivery->estimate_delivery_date,
                            'address' => $delivery->address
                        ]);
                    }
                    $delivery->timelines()->create([
                        'event' => 'create_delivery',
                        'description' => 'Liên kết với vận đơn của nhà vận chuyển ngoài.',
                    ]);

                    foreach ($delivery->orders as $order) {
                        SendStartedDeliveryZaloSms::dispatch($customer->id, $order->id, $delivery->estimate_delivery_date);
                    }

                    $delivieries_result[] = $delivery;
                }


                DB::commit();
                return $delivieries_result;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
