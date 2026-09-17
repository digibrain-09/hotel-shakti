<?php

namespace App\Http\Controllers;

use App\Jobs\Jobname–queued;
use App\Models\AddOn;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\Queue;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

require_once app_path('dompdf/autoload.inc.php');

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class AddOnController extends Controller
{
    public function index(Request $request)
    {
        $restaurant_id = session('restaurant_id');
        $search = trim($request->search);

        $query = DB::table('add_ons')
            ->select('add_ons.*');

        // Apply restaurant filter only if exists
        if (!empty($restaurant_id)) {
            $query->where('add_ons.restaurant_id', $restaurant_id);
        }

        // Apply search only if user typed something
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('add_ons.name', 'LIKE', "%{$search}%")
                    ->orWhere('add_ons.type', 'LIKE', "%{$search}%");
            });
        }

        $add_ons = $query->paginate(10)->withQueryString();

        return view('add_on.index', compact('add_ons'));
    }

    public function store(Request $request)
    {
        $restaurant_id = session()->get('restaurant_id');

        $this->validate($request, [
            'name' => 'required',
            'price' => 'required',
            'type' => 'required',
        ]);

        $data = new AddOn();

        $data->name = ucwords(strtolower($request->name));
        $data->price = $request->price;
        $data->restaurant_id = $restaurant_id;
        $data->type = $request->type;
        $data->save();

        // return redirect()->route('add_on.index');
        // return redirect()->back()->with('success', 'Add-on added successfully!');
        return response()->json([
            'success' => true,
            'message' => 'Add-on added successfully!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $restaurant_id = session()->get('restaurant_id');


        $data = AddOn::find($id);
        $data->name = ucwords(strtolower($request->input('name')));
        $data->price = $request->input('price');
        $data->restaurant_id = $restaurant_id;
        $data->type =  $request->input('type');

        $data->update();

        // return redirect()->route('add_on.index');
        // return redirect()->back()->with('success', 'Add-on updated successfully!');
        return response()->json([
            'success' => true,
            'message' => 'Add-on updated successfully!'
        ]);
    }
    public function destroy($id)
    {
        // $addon->delete();
        $data = AddOn::find($id);
        $data->delete();
        // return redirect()->route('add_on.index');
        return redirect()->back()->with('success', 'Add-on deleted successfully!');
    }
}
