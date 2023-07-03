<?php

namespace App\Repositories\Business;

use App\Enums\OrderStatus as EnumsOrderStatus;
use App\Models\Business\Delivery;
use App\Models\Business\DeliveryToken;
use App\Models\Business\Order;
use App\Models\Business\OrderDelivery;
use App\Models\Master\Customer;
use App\Models\Master\DeliveryPartner;
use App\Models\Master\Warehouse;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DeliveryRepository extends RepositoryAbs
{
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

                $generated_token = null;
                if ($delivery_partner->is_external) {
                    $generated_token = $this->createDeliveryTokenExternal($delivery, $delivery_partner);
                } else {
                    $generated_token = $this->createDeliveryTokenInternal($delivery, $delivery_partner);
                }
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
                DB::commit();

                $result = array(
                    'delivery_id' => $delivery->id,
                    'qr_token' => $generated_token,
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

    private function createDeliveryTokenInternal($delivery, $delivery_partner)
    {
        $token = $delivery->tokens()->create([
            'delivery_id' => $delivery->id,
            'delivery_partner_id' => $delivery_partner->id,
            'token' => Str::uuid()->toString(),
            'is_primary' => true,
        ]);
        return $token;
    }

    private function createDeliveryTokenExternal($delivery, $delivery_partner)
    {
        $token = DeliveryToken::create([
            'delivery_id' => $delivery->id,
            'delivery_partner_id' => $delivery_partner->id,
            //'token' => '1234567890',
            'is_primary' => true,
        ]);
        return null;
    }

    public function deleteDelivery($id)
    {
        try {
            DB::beginTransaction();

            $delivery = Delivery::find($id);
            if (!$delivery) {
                $this->message = 'Đơn vận chuyển không tồn tại.';
                return false;
            } else {
                $delivery->tokens()->delete();
                foreach ($delivery->orders as $order) {
                    $order->status_id = EnumsOrderStatus::Pending;
                    $order->save();
                }
                OrderDelivery::where('delivery_id', $delivery->id)->delete();
                $delivery->delete();
            }
            DB::commit();

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
