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

            //$app_id = '4589579355561746606';
            $app_id = '1994287696618217604';
            $redirect_url = 'https://zalo.me/s/' . $app_id . '/?env=DEVELOPMENT&version=zdev-640a79e5&delivery=' . $delivery->id;

            return $redirect_url;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
