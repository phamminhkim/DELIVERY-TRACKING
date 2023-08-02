<?php

namespace App\Utilities;

class QrCodeUtility
{
    public static function generateImage($token) {
        $qr_code = \QrCode::size(80)->errorCorrection('M')->generate($token);

        return $qr_code;
    }
}
