<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
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

        $order = Order::create([
            'table_id'       => $request->table_id,
            'admin_id'       => $request->admin_id,
            'status'         => 'pending',
            'total_amount'   => 0,
            'payment_method' => $request->payment_method,
        ]);

        return response()->json($order, 201);
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
