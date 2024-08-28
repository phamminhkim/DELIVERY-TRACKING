<?php

namespace App\Utilities;

use DateTime;
use Exception;
use Carbon\Carbon;

class FormatDateUtility
{
    public static function isDate($value)
    {
        if (!$value) return false;
        try {
            new DateTime($value);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function formatDate2Date($src_date_format, $dest_date_format, $date)
    {
        $result = null;
        try {
            if ($date && $date != "0/00/00") {
                $result =  Carbon::createFromFormat($src_date_format, $date)->format($dest_date_format);
            }
        } catch (\Throwable $th) {
            $result = null;
        }
        return $result;
    }

}
