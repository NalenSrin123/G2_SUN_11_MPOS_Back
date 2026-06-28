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

        // 1. Create a temporary filename using .svg on the public disk
        $tempPath = "qrcodes/table_{$request->table_number}.svg";
        $url = url("/table/{$request->table_number}");
        
        // Removed ->format('png') because SVG is the native default format
        $svg = QrCode::size(300)->generate($url);
        Storage::disk('public')->put($tempPath, $svg);

        // 2. Create the table record
        $table = RestaurantTable::create([
            'table_number' => $request->table_number,
            'qr_code'      => $tempPath,
            'status'       => 'closed',
        ]);

        // 3. Rename the file to use the real database ID
        $finalPath = "qrcodes/table_{$table->id}.svg";
        Storage::disk('public')->move($tempPath, $finalPath);

        // 4. Update the DB with the final SVG path
        $table->qr_code = $finalPath;
        $table->save();

        return response()->json($table, 201);
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