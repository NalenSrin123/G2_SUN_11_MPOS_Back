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

        $tableNumber = $request->table_number;

        // This is what the QR code will actually link to
        // e.g. a public menu/order page for this table
        $qrUrl = config('app.frontend_url') . "/table/{$tableNumber}";
        // Generate the QR code as a PNG binary string
        $qrImage = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate($qrUrl);

        $path = "qrcodes/table-{$tableNumber}.svg";
        Storage::disk('public')->put($path, $qrImage);

        $qrImageUrl = asset('storage/app/public/' . $path);

        $table = RestaurantTable::create([
            'table_number' => $tableNumber,
            'qr_code'      => $qrImageUrl, // store the relative path, not the binary
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
