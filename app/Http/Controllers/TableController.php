<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::orderBy('table_number')->get();
        return response()->json($tables);
    }

    public function show($id)
    {
        $table = RestaurantTable::findOrFail($id);
        return response()->json($table);
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|integer|unique:restaurant_tables,table_number',
        ]);

        $table = RestaurantTable::create([
            'table_number' => $request->table_number,
            'qr_code'      => 'https://g2-sun-11-mpos-back-1.onrender.com/api/v1/tables/' . $request->table_number,
            'status'       => 'closed',
        ]);

        return response()->json($table);
    }

    public function update(Request $request, $id)
    {
        $table = RestaurantTable::findOrFail($id);

        $request->validate([
            'table_number' => "sometimes|required|integer|unique:restaurant_tables,table_number,{$id}",
            'status'       => 'sometimes|required|in:open,closed',
        ]);

        $table->update($request->only(['table_number', 'status']));

        return response()->json($table);
    }

    public function destroy($id)
    {
        $table = RestaurantTable::findOrFail($id);

        if ($table->qr_code && Storage::disk('public')->exists($table->qr_code)) {
            Storage::disk('public')->delete($table->qr_code);
        }

        $table->delete();

        return response()->json(['message' => 'Table deleted successfully.']);
    }
}
