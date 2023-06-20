<?php

namespace App\Http\Controllers\BaseController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    public function sendResponse($result, $message = '', $code = 200)
    {

        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }
    public function sendError($error, $errorMessage = [], $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $error
        ];
        if (!empty($errorMessage)) {
            $response['errors'] = $errorMessage;
        }
        return response()->json($response, $code);
    }

    public function sendOk()
    {
        return response();
    }
    public function sendSuccess($message)
    {
        $response = [
            'success' => true,
            'message' => $message
        ];
        return response()->json($response, 200);
    }
    public function sendFailedWithMessage($message)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];
        return response()->json($response, 200);
    }
    public function sendFailedWithStatusCode($message, $code)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];
        return response()->json($response, $code);
    }
}