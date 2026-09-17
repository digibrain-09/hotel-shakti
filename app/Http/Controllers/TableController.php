<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\restaurant_Table;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->search);
        $restaurant_id = $request->restaurant_id;

        $restaurants = Restaurant::all();

        $query = DB::table('restaurant_tables')
            ->join('restaurants', 'restaurants.id', '=', 'restaurant_tables.restaurant_id')
            ->select('restaurant_tables.*', 'restaurants.restaurant_name');

        // Filter by restaurant (optional)
        if (!empty($restaurant_id)) {
            $query->where('restaurant_tables.restaurant_id', $restaurant_id);
        }

        // Search across table name / number / restaurant
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('restaurant_tables.table_name', 'LIKE', "%{$search}%")
                    ->orWhere('restaurants.restaurant_name', 'LIKE', "%{$search}%");
            });
        }

        $tables = $query->paginate(10)->withQueryString();

        return view('table.index', compact('restaurants', 'tables'));
    }
    public function store(Request $request)
    {

        $this->validate($request, [
            'table_name' => 'required',
            'qr_code' => 'required',
            'qr_code.*' => 'mimes:doc,docx,png,jpge,jpg',
        ]);

        $data = new Table();
        if ($request->file('qr_code')) {
            $file = $request->file('qr_code');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('/table_qr_code'), $filename);
            $data['qr_code'] = $filename;
        }

        $data->table_name = $request->table_name;
        $data->restaurant_id = $request->input('restaurantId');
        $data->seating_type = $request->input('seating_type');
        $data->save();

        // return redirect()->route('table.index');
        // return redirect()->back()->with('success', 'Table added successfully!');
        return response()->json([
            'success' => true,
            'message' => 'Table added successfully!'
        ]);
    }
    public function update(Request $request, $id)
    {
        $data = Table::find($id);
        $data->table_name = $request->input('table_name');
        $data->restaurant_id = $request->input('restaurantId');
        $data->seating_type = $request->input('seating_type');

        if ($request->hasFile('qr_code')) {
            $file = $request->file('qr_code');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('/table_qr_code'), $filename);
            $data['qr_code'] = $filename;
        }
        $data->update();
        // return redirect()->route('table.index');
        // return redirect()->back()->with('success', 'Table updated successfully!');
        return response()->json([
            'success' => true,
            'message' => 'Table updated successfully!'
        ]);
    }
    public function destroy(Table $table)
    {
        $table->delete();
        // return redirect()->route('table.index');
        return redirect()->back()->with('success', 'Table deleted successfully!');
    }
}
