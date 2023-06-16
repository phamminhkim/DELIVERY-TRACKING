<?php
namespace App\Http\Controllers;
use App\Models\Shared\Order;
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
        $orders->company_id = $request->input('company_id');
        $orders->sap_do = $request->input('sap_do');
        $orders->customer_id = $request->input('customer_id');
        $orders->delivery_address = $request->input('delivery_address');
        $orders->weight = $request->input('weight');
        $orders->note = $request->input('note');
        $orders->status_id = $request->input('status_id');
        $orders->delivery_id = $request->input('delivery_id');
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
