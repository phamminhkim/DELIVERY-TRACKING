<?php
namespace App\Http\Controllers;
use App\Models\Business\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::all();
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    
    //create order
    public function store(Request $request)
    {
        $orders = new Order();
        $orders->company_code = $request->input('company_id');
        $orders->customer_id = $request->input('customer_id');        
        $orders->sap_so_number = $request->input('sap_so_number');
        $orders->sap_so_created_date = $request->input('sap_so_created_date');
        $orders->sap_po_number = $request->input('sap_po_number');
        $orders->sap_do_number = $request->input('sap_do_number');
        $orders->status_id = $request->input('status_id');
        $orders->warehouse_id = $request->input('warehouse_id');
        $orders->save();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    // Lấy thông tin một đơn hàng
    public function show($id)
    {
        $orders = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
    //update order 
    public function update(Request $request, $id)
    {
        $orders = Order::findOrFail($id);
        $orders->fill($request->all());
        $orders->save();
    
        return response()->json([
            'message' => 'Order updated successfully',
            'data' => $orders
        ]);
    }
    //delete order
    public function destroy($id)
    {
        $orders = Order::findOrFail($id);
        $orders->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }
}  
