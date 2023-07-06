<?php

namespace App\Repositories\Business;

use App\Enums\OrderStatus as EnumsOrderStatus;
use App\Models\Business\Delivery;
use App\Models\Business\DeliveryToken;
use App\Models\Business\Order;
use App\Models\Master\Customer;
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
                $this->message = 'Mã QR không hợp lệ.';
                return false;
            }
            $delivery = Delivery::find($delivery_token->delivery_id);
            if (!$delivery) {
                $this->message = 'Đơn vận chuyển không tồn tại.';
                return false;
            }

            if ($delivery->partner->is_external) {
                $this->message = 'Đơn vị vận chuyển không hỗ trợ.';
                return false;
            } else {
                $params = array(
                    'delivery' => $delivery->id
                );
                if ($delivery->complete_delivery_date) {
                    $app_env = config('services.zalo.customer_app_env');
                    $app_id = config('services.zalo.customer_app_id');
                    $app_version = config('services.zalo.customer_app_version');
                } else {
                    $app_env = config('services.zalo.driver_app_env');
                    $app_id = config('services.zalo.driver_app_id');
                    $app_version = config('services.zalo.driver_app_version');
                }

                if (
                    $app_env && $app_id
                ) {
                    if ($app_env == 'development') {
                        $params['env'] = $app_env;
                        $params['version'] = $app_version;
                    }
                    $redirect_url = 'https://zalo.me/s/' . $app_id . '/?' . http_build_query($params);
                    return $redirect_url;
                } else {
                    $this->message = 'Không tìm thấy thông tin ứng dụng Zalo.';
                    return false;
                }
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
