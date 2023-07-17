<?php

namespace App\Utilities;

use App\Models\Master\MenuRouter;
use App\User;
use Illuminate\Support\Facades\DB;

class UniqueIdUtility
{
    public static function generateUniqueId($table, $column, $prefix, $length)
    {
        $unique = false;
        $unique_id = '';

        while (!$unique) {
            // Generate a random string
            $id = $prefix . substr(str_shuffle(str_repeat($x = '123456789ABCDEFGHJKMNPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);

            // Check if this ID already exists in the database
            $exists = DB::table($table)->where($column, $id)->first();

            if (!$exists) {
                $unique_id = $id;
                $unique = true;
            }
        }

        return $unique_id;
    }

    public static function generateDeliveryUniqueCode($delivery_partner)
    {
        //AAYY00XXXXXX
        // AA: Mã NVC
        // YY: 2 chữ số của năm
        // 00: 2 số cố định 00
        // XXXXXX: Random kí tự A-Z1-9 (bỏ kí tự I, L, O, 0)
        $prefix = $delivery_partner->code . date('y') . '00';

        $delivery_code = UniqueIdUtility::generateUniqueId('deliveries', 'delivery_code', $prefix, 6);

        return $delivery_code;
    }
}
