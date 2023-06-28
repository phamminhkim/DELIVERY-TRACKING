<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Models\Business\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends ResponseController
{
    public function getAvailableOrders()
    {
        $query = Order::all();

        return $this->responseSuccess($query);
    }

    //create order
    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $validator = Validator::make($request->all(), []);

        $failed = $validator->fails();
        $isErr = false;

        if ($failed || $isErr) {
            return  $this->responseError('Validation', $validator->errors());
        } else {
            try {
                // Create new order
                $order = new Order();
                $order->company_code = $request->input('company_id');
                $order->customer_id = $request->input('customer_id');
                $order->sap_so_number = $request->input('sap_so_number');
                $order->sap_so_created_date = $request->input('sap_so_created_date');
                $order->sap_po_number = $request->input('sap_po_number');
                $order->sap_do_number = $request->input('sap_do_number');
                $order->status_id = $request->input('status_id');
                $order->warehouse_id = $request->input('warehouse_id');
                $order->save();

                return $this->responseSuccess($order);
            } catch (\Exception $e) {
                return $this->responseError('Exception', $e->getMessage());
            }
        }
    }

    public function show($id)
    {
        $orders = Order::findOrFail($id);

        $result = array();
        $result['data'] =  array();
        $result['data'] = $orders;
        $result['success'] = "1";

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function update(Request $request, $id)
    {

        $result = array();
        $result['data'] =  array();
        $result['data']['success']  = 0;
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $validator = Validator::make($request->all(), []);

        $failed = $validator->fails();
        $isErr = false;
        if ($failed) {
            $result['data']['errors']  = $validator->errors();
        } else {
            $orders = Order::findOrFail($id);
            //$orders->id = $request->input('id');
            $orders->company_code = $request->input('company_id');
            $orders->customer_id = $request->input('customer_id');
            $orders->sap_so_number = $request->input('sap_so_number');
            $orders->sap_so_created_date = $request->input('sap_so_created_date');
            $orders->sap_po_number = $request->input('sap_po_number');
            $orders->sap_do_number = $request->input('sap_do_number');
            $orders->status_id = $request->input('status_id');
            $orders->warehouse_id = $request->input('warehouse_id');

            if ($orders->save()) {
                $result['data']['success']  = 1;
                $result['data']  = $orders;
            }
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);

    }
    public function destroy($id)
    {

        $deleteOrder = Order::findOrFail($id);
        $result = array();
        $result['data'] =  array();
        $result['data']['success']  = 0;
        if ($deleteOrder->delete()) {
            $result['data']['success']  = 1;
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
