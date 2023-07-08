<?php

namespace App\Repositories\Business;

use App\Enums\OrderStatus as EnumsOrderStatus;
use App\Models\Business\Delivery;
use App\Models\Business\DeliveryToken;
use App\Models\Business\DeliveryTokenScan;
use App\Models\Business\Order;
use App\Models\Business\OrderDelivery;
use App\Models\Business\OrderDriverConfirm;
use App\Models\Master\DeliveryPartner;
use App\Models\Master\Image;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DeliveryRepository extends RepositoryAbs
{
    public function getDeliveryByQrScan($qr_token)
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

                return $this->getDeliveryById($delivery_token->delivery_id);
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
    public function getDeliveryById($delivery_id)
    {
        try {
            $delivery = Delivery::find($delivery_id);
            if (!$delivery) {
                $this->message = 'Đơn vận chuyển không tồn tại.';
                return false;
            }

            $delivery->load(['company', 'partner', 'orders', 'orders.status', 'orders.detail', 'orders.receiver', 'orders.driver_confirms', 'orders.driver_confirms.images']);
            foreach ($delivery->orders as $order) {
                $order->unsetRelation('pivot');
            }
            return $delivery;
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
                DB::beginTransaction();
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

                $confirm = $order->driver_confirms()->create([
                    'complete_delivery_date' => now(),
                    'confirm_status' => $this->data['confirm_status'],
                    'driver_phone' => $this->current_user->phone_number,
                    'driver_name' => $this->data['driver_name'],
                    'driver_note' => $this->data['driver_note'] ?? '',
                    'driver_plate_number' => $this->data['driver_plate_number'],
                ]);
                $this->storeImageImages($confirm, $this->data['images'] ?? []);
                if ($this->data['confirm_status'] == 'fully') {
                    $order->update(['status_id' => EnumsOrderStatus::Delivered]);
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
            DB::commit();

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    private function storeImageImages($confirm, $upload_images)
    {
        foreach ($upload_images as $upload_image) {
            $image_data = $upload_image['thumbUrl'];

            $name = uniqid();
            $extension = substr($image_data, strpos($image_data, "/") + 1, strpos($image_data, ";") - strpos($image_data, "/") - 1);
            $image_base64 = substr($image_data, strpos($image_data, ",") + 1);

            $image_raw_data = base64_decode($image_base64);
            $image_props = getimagesizefromstring($image_raw_data);
            $width = $image_props[0];
            $height = $image_props[1];
            $size = strlen($image_raw_data);

            $image = new Image();
            $image->name = $name;
            $image->owner_id = $this->current_user->id;
            $image->ext = $extension;
            $image->width = $width;
            $image->height = $height;
            $image->size = $size;

            $file_name = $image->name . '.' . $image->ext;
            $image->url = 'images/' . $file_name;

            Storage::disk('images')->put($file_name, $image_data);

            $confirm->images()->save($image, ['imageable_id' => $confirm->id, 'imageable_type' => OrderDriverConfirm::class]);
        }
    }

    public function createDelivery()
    {
        try {
            $validator = Validator::make($this->data, [
                'company_code' => 'required|string|exists:companies,code',
                'delivery_partner_code' => 'required|string|exists:delivery_partners,code',
                'orders' => 'required|array',
                'orders.*.id' => 'required|uuid|exists:orders',
            ], [
                'company_code.required' => 'Mã công ty là bắt buộc.',
                'company_code.exists' => 'Mã công ty không tồn tại.',
                'delivery_partner_code.required' => 'Mã đối tác giao hàng là bắt buộc.',
                'delivery_partner_code.exists' => 'Mã đối tác giao hàng không tồn tại.',
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
                $delivery = Delivery::create([
                    'company_code' => $this->data['company_code'],
                    'delivery_partner_id' => $delivery_partner->id,
                    'start_delivery_at' => null,
                    'complete_delivery_at' => null,
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
                    ]);
                }
                $qr_code = \QrCode::size(200)->errorCorrection('H')->generate($generated_token->token);
                DB::commit();

                $result = array(
                    'delivery_id' => $delivery->id,
                    'qr_token' => env('APP_URL') . '/scan-qr/' . strval($qr_code),
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
            ], [
                'orders.*.required' => 'Mã đơn hàng là bắt buộc.',
                'orders.*.uuid' => 'Mã đơn hàng không đúng định dạng.',
                'orders.*.exists' => 'Mã đơn hàng không tồn tại.',
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
}
