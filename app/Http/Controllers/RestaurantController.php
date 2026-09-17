<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantAdmin;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use App\Models\Table;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $restaurant_id = session('restaurant_id');
        $search = trim($request->search);

        // Default counts
        $CategoryCount = 0;
        $ItemCount = 0;
        $TableCount = 0;
        $CouponCount = 0;

        // If restaurant_id exists → show only that restaurant
        if (!empty($restaurant_id)) {

            $restaurants = Restaurant::where('id', $restaurant_id)->paginate(10);

            $CategoryCount = Category::where('restaurant_id', $restaurant_id)->count();
            $ItemCount     = Item::where('restaurant_id', $restaurant_id)->count();
            $TableCount    = Table::where('restaurant_id', $restaurant_id)->count();
            $CouponCount   = Coupon::where('restaurant_id', $restaurant_id)->count();
        } else {

            // Global restaurant listing + search
            $query = Restaurant::query();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('restaurant_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            $restaurants = $query->paginate(10)->withQueryString();
        }

        $adminEmail = auth()->user()->email;

        $AdminExists = User::where('email', $adminEmail)->where('role', 'admin')->exists();

        return view(
            'restaurant.index',
            compact('restaurants', 'CategoryCount', 'ItemCount', 'TableCount', 'CouponCount', 'AdminExists')
        )->with('i', ($restaurants->currentPage() - 1) * $restaurants->perPage());
    }

    public function checkEmail(Request $request)
    {
        $emailExists = Restaurant::where('email', $request->email)->exists() || User::where('email', $request->email)->exists();

        return response()->json([
            'exists' => $emailExists
        ]);
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'restaurant_name' => 'required',
            'restaurant_nav' => 'required',
            'logo' => 'required',
            'logo.*' => 'mimes:doc,pdf,docx,png,jpge,jpg',
            'address' => 'required',
            'owner_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'geofence_radius' => 'required',
        ]);

        $data = new Restaurant();
        $data2 = new User();
        if ($request->file('logo')) {
            $file = $request->file('logo');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('/restaurant_logo'), $filename);
            $data['logo'] = $filename;
        }

        $data->restaurant_name = $request->restaurant_name;
        $data->restaurant_nav = $request->restaurant_nav;
        $data->address = $request->address;
        $data->latitude = $request->latitude;
        $data->longitude = $request->longitude;
        $data->geofence_radius = $request->geofence_radius;
        $data->owner_name = $request->owner_name;
        $data->email = $request->email;
        $data->password = Hash::make($request->password);
        $data->phone = $request->phone;
        $data->CGST = $request->CGST;
        $data->SGST = $request->SGST;
        $data->GST_status = true;
        $data->save();

        $data2->name = $request->owner_name;
        $data2->email = $request->email;
        $data2->password = Hash::make($request->password);
        $data2->role = 'restaurant_admin';
        $data2->save();

        // return redirect()->route('restaurant.index');
        // return redirect()->back()->with('success', 'restaurant added successfully!');
        return response()->json([
            'success' => true,
            'message' => 'Restaurant added successfully!'
        ]);
    }
    public function update(Request $request, $id)
    {
        $data = Restaurant::find($id);

        $users = User::where('email', $data->email)->get();

        foreach ($users as $user) {
            $user->name = $request->input('owner_name');
            $user->email = $request->input('email');
            if ($request->input('password')) {
                $user->password = Hash::make($request->input('password'));
            }
            $user->role = 'restaurant_admin';
            $user->save(); // Use save() instead of update()
        }

        // return $request->input('restaurant_name');

        $data->restaurant_name = $request->input('restaurant_name');
        $data->restaurant_nav = $request->input('restaurant_nav');
        $data->address = $request->input('address');
        $data->latitude = $request->input('latitude');
        $data->longitude = $request->input('longitude');
        $data->geofence_radius = $request->input('geofence_radius');
        $data->owner_name = $request->input('owner_name');
        $data->email = $request->input('email');
        if ($request->input('password')) {
            $data->password = Hash::make($request->input('password'));
        }
        $data->phone = $request->input('phone');
        $data->CGST = $request->CGST;
        $data->SGST = $request->SGST;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('/restaurant_logo'), $filename);
            $data['logo'] = $filename;
        }
        $data->save();


        // return $data;
        // return redirect()->route('restaurant.index');
        // return redirect()->back()->with('success', 'restaurant updated successfully!');
        return response()->json([
            'success' => true,
            'message' => 'Restaurant updated successfully!'
        ]);
    }
    public function destroy(Restaurant $restaurant)
    {
        User::where('email', $restaurant->email)->delete();

        $restaurant->delete();
        // return redirect()->route('restaurant.index');
        return redirect()->back()->with('success', 'Restaurant deleted successfully!');
    }

    public function updateStatus(Request $request)
    {
        $restaurant = Restaurant::find($request->id);
        if ($restaurant) {
            $restaurant->GST_status = $request->status;
            $restaurant->save();
            return response()->json(['success' => true, 'message' => 'GST status updated successfully!']);
        }
        return response()->json(['success' => false, 'message' => 'Item not found!']);
    }
}
