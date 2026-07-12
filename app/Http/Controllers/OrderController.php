<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['restaurantTable', 'items'])->orderBy('created_at', 'desc')->get();

        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::with(['restaurantTable', 'items'])->findOrFail($id);

        return response()->json($order);
    }



    public function store(Request $request)
{
    $request->validate([
        'table_id'       => 'required|exists:restaurant_tables,id',
        'admin_id'       => 'required|exists:admins,id',
        'payment_method' => 'nullable|string',
    ]);

    // 1. Check database details live during the request
    $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
    $dbPort = \Illuminate\Support\Facades\DB::connection()->getConfig('port');

    // 2. Instantiate manually and explicitly save to look for errors
    $order = new Order();
    $order->table_id = $request->table_id;
    $order->admin_id = $request->admin_id;
    $order->status = 'pending';
    $order->total_amount = 0;
    $order->payment_method = $request->payment_method;

    try {
        // saveOrFail() forces an exception if the database rolls back
        $order->saveOrFail();
        
        return response()->json([
            'status' => 'Success',
            'message' => 'Order successfully saved according to Eloquent!',
            'active_database' => $dbName,
            'active_port' => $dbPort,
            'saved_data' => $order
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'Failed',
            'message' => 'The database explicitly rejected the save!',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status'         => 'sometimes|required|in:pending,confirmed,completed,cancelled',
            'payment_method' => 'sometimes|required|string',
            'total_amount'   => 'sometimes|required|numeric|min:0',
        ]);

        $order->update($request->only(['status', 'payment_method', 'total_amount']));

        return response()->json($order);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully.']);
    }
}