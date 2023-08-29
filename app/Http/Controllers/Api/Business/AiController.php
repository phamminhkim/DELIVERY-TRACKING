<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Models\Business\Order;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AiController extends ResponseController
{
    public function extractOrder(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->extractOrder();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
