<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function index()
    {
        $items = OrderItem::with(['order', 'product'])->get();

        return response()->json($items);
    }

    public function show($id)
    {
        $item = OrderItem::with(['order', 'product'])->findOrFail($id);

        return response()->json($item);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $item = OrderItem::create([
            'order_id'   => $request->order_id,
            'product_id' => $request->product_id,
            'quantity'   => $request->quantity,
            'unit_price' => $request->unit_price,
        ]);

        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = OrderItem::findOrFail($id);

        $request->validate([
            'quantity'   => 'sometimes|required|integer|min:1',
            'unit_price' => 'sometimes|required|numeric|min:0',
        ]);

        $item->update($request->only(['quantity', 'unit_price']));

        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = OrderItem::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Order item deleted successfully.']);
    }
}