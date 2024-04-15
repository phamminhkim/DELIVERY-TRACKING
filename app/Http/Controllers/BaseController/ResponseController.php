<?php

namespace App\Http\Controllers\BaseController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function responseOk()
    {
        return response()->json([
            'success' => true
        ]);
    }
    public function responseSuccess($result, $message = '', $code = 200)
    {
        $response = [
            'success' => true,
            'data'    => $result,
        ];
        if ($message != '') {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }
    public function response($result, $message = '', $code = 200)
    {
        $response = [
            'data'    => $result,
        ];
        if ($message != '') {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }
    public function responseError($error_message, $error_array = [], $code = 400)
    {
        $response = [
            'success' => false
        ];
        if (!empty($error_message)) {
            $response['message'] = $error_message;
        }
        if (!empty($error_array)) {
            $response['errors'] = $error_array;
        }
        return response()->json($response, $code);
    }
}
