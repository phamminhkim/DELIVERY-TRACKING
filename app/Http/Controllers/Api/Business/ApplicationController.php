<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Models\Business\Order;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends ResponseController
{
    public function getTargetApplicationUrl(Request $request, $qr_code)
    {
        $handler = BusinessRepository::applicationRequest($request);
        $url = $handler->getTargetApplicationUrl($qr_code);

        if ($url) {
            return redirect($url);
        } else {
            return  redirect('/errors', 404, $handler->getMessage());
        }
    }
}
