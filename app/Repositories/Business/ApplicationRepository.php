<?php

namespace App\Repositories\Business;

use App\Enums\OrderStatus as EnumsOrderStatus;
use App\Models\Business\Delivery;
use App\Models\Business\DeliveryToken;
use App\Models\Business\DeliveryTokenScan;
use App\Models\Business\Order;
use App\Models\Master\Customer;
use App\Models\Master\CustomerPhone;
use App\Models\Master\Warehouse;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApplicationRepository extends RepositoryAbs
{
    public function getTargetApplicationUrl($qr_token)
    {
        try {
            $delivery_token = DeliveryToken::where('token', $qr_token)->first();
            if (!$delivery_token) {
                DeliveryTokenScan::create([
                    'token_id' => null,
                    'scan_by' => $this->current_user ? $this->current_user->id : null,
                    'scan_at' => now(),
                    'is_success' => false,
                    'result' => 'Token not found'
                ]);
                $this->message = 'Mã QR không hợp lệ.';
                return false;
            }
            $delivery = Delivery::find($delivery_token->delivery_id);
            if (!$delivery) {
                DeliveryTokenScan::create([
                    'token_id' => $delivery_token->id,
                    'scan_by' => $this->current_user ? $this->current_user->id : null,
                    'scan_at' => now(),
                    'is_success' => false,
                    'result' => 'Delivery not found'
                ]);
                $this->message = 'Đơn vận chuyển không tồn tại.';
                return false;
            }

            if ($delivery->partner->is_external) {
                DeliveryTokenScan::create([
                    'token_id' => $delivery_token->id,
                    'scan_by' => $this->current_user ? $this->current_user->id : null,
                    'scan_at' => now(),
                    'is_success' => false,
                    'result' => 'Unsupported delivery partner'
                ]);
                $this->message = 'Đơn vị vận chuyển không hỗ trợ.';
                return false;
            } else {
                $redirect_url = $this->getRedirectUrlForInternalSystem($delivery);

                DeliveryTokenScan::create([
                    'token_id' => $delivery_token->id,
                    'scan_by' => $this->current_user ? $this->current_user->id : null,
                    'scan_at' => now(),
                    'is_success' => true,
                    'result' => 'Redirect to ' . $redirect_url
                ]);

                return $redirect_url;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();

            DeliveryTokenScan::create([
                'token_id' => $delivery_token ? $delivery_token->id : null,
                'scan_by' => $this->current_user ? $this->current_user->id : null,
                'scan_at' => now(),
                'is_success' => false,
                'result' => $exception->getMessage()
            ]);
        }
    }

    private function getRedirectUrlForInternalSystem($delivery)
    {
        $is_customer = CustomerPhone::where('phone_number', $this->current_user->phone_number)->first();
        if ($is_customer) {
            $app_env = config('services.zalo.customer_app_env');
            $app_id = config('services.zalo.customer_app_id');
            $app_version = config('services.zalo.customer_app_version');
        } else {
            $app_env = config('services.zalo.driver_app_env');
            $app_id = config('services.zalo.driver_app_id');
            $app_version = config('services.zalo.driver_app_version');
        }

        $params = array();
        if (
            $app_env && $app_id
        ) {
            if ($app_env == 'DEVELOPMENT' || $app_env == 'TESTING') {
                $params['env'] = $app_env;
                $params['version'] = $app_version;
            }
            $params['delivery'] = $delivery->id;
            $redirect_url = 'https://zalo.me/s/' . $app_id . '/?' . http_build_query($params);

            return $redirect_url;
        } else {
            $this->message = 'Không tìm thấy thông tin ứng dụng Zalo.';
            return false;
        }
    }
}
