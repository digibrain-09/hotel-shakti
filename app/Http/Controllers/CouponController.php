<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Support\Facades\Cookie;


class CouponController extends Controller
{
    public function index(Request $request)
    {
        $restaurant_id = session('restaurant_id');
        $search = trim($request->search);

        $query = DB::table('coupons')
            ->leftJoin('categories', 'categories.id', '=', 'coupons.categoryId')
            ->select(
                'coupons.*',
                'categories.category_name'
            );

        // Apply restaurant filter only if exists
        if (!empty($restaurant_id)) {
            $query->where('coupons.restaurant_id', $restaurant_id);
        }

        // Apply search only if user typed something
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('coupons.code', 'LIKE', "%{$search}%")
                    ->orWhere('coupons.type', 'LIKE', "%{$search}%")
                    ->orWhere('coupons.discount_mode', 'LIKE', "%{$search}%")
                    ->orWhere('categories.category_name', 'LIKE', "%{$search}%");
            });
        }

        $coupons = $query->paginate(10)->withQueryString();

        $categories = DB::table('categories')->where('categories.restaurant_id', $restaurant_id)->get('categories.*');

        return view('coupons.index', compact('coupons', 'categories'));
    }

    public function store(Request $request)
    {
        $restaurant_id = session()->get('restaurant_id');

        $this->validate($request, [
            'code' => 'required',
            'type' => 'required',
            'value' => 'required',
            'start_at' => 'required',
            'expires_at' => 'required',
        ]);

        $data = new Coupon();

        $data->code = strtoupper($request->code);
        $data->type = $request->type;

        $data->value = $request->value;
        $data->start_at = $request->start_at;
        $data->expires_at = $request->expires_at;
        $data->restaurant_id = $restaurant_id;
        $data->min_order = $request->min_order;



        if ($request->type == 'Flat Amount Discount') {
            $data->discount_mode = 'fixed';
        } else if ($request->type == 'Percentage Discount') {
            $data->discount_mode = 'percent';
        } else {
            $data->discount_mode = $request->discount_mode;
        }


        if ($request->type == 'Category-Specific Discount') {
            $data->categoryId = $request->categoryId;
        } else if ($request->type == 'Time-Based Discount') {
            $start = Carbon::parse($request->start_time);
            $end   = Carbon::parse($request->end_time);


            $data->start_time = $start;
            $data->end_time = $end;
        }

        $applicableDays = $request->has('applicable_days')
            ? json_encode($request->applicable_days)
            : null;

        $data->applicable_days = $applicableDays;

        $data->save();

        // return redirect()->route('coupons.index');
        // return redirect()->back()->with('success', 'Coupon added successfully!');
        return response()->json([
            'success' => true,
            'message' => 'Coupon added successfully!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $restaurant_id = session()->get('restaurant_id');


        $data = Coupon::find($id);
        $data->code = strtoupper($request->input('code'));
        $data->type =  $request->input('type');
        $data->value = $request->input('value');
        $data->expires_at = $request->input('expires_at');
        $data->restaurant_id = $restaurant_id;
        $data->min_order = $request->input('min_order');
        $data->start_at = $request->input('start_at');

        if ($request->input('type') == 'Flat Amount Discount') {
            $data->discount_mode = 'fixed';
        } else if ($request->input('type') == 'Percentage Discount') {
            $data->discount_mode = 'percent';
        } else {
            $data->discount_mode = $request->input('discount_mode');
        }

        if ($request->input('type') == 'Category-Specific Discount') {

            $data->categoryId = $request->input('categoryId');
            $data->start_time = 0;
            $data->end_time = 0;
        } else if ($request->input('type') == 'Time-Based Discount') {

            $start = Carbon::parse($request->input('start_time'));
            $end   = Carbon::parse($request->input('end_time'));

            $data->categoryId = 0;
            $data->start_time = $start;
            $data->end_time = $end;
        } else {
            $data->categoryId = 0;
            $data->start_time = 0;
            $data->end_time = 0;
        }

        $categoryId = $request->has('categoryId')
            ? $request->categoryId
            : 0;
        $data->categoryId = $categoryId;

        $applicableDays = $request->has('applicable_days')
            ? json_encode($request->applicable_days)
            : null;

        $data->applicable_days = $applicableDays;

        $data->update();

        // return redirect()->route('coupons.index');
        // return redirect()->back()->with('success', 'Coupon updated successfully!');
        return response()->json([
            'success' => true,
            'message' => 'Coupon updated successfully!'
        ]);
    }
    public function destroy($id)
    {
        // $addon->delete();
        $data = Coupon::find($id);
        $data->delete();
        // return redirect()->route('coupons.index');
        return redirect()->back()->with('success', 'Coupon deleted successfully!');
    }

    public function apply(Request $request, \App\Services\CouponService $coupons)
    {
        $request->validate(['code' => 'required|string']);

        $lines = $this->currentOrderLines();

        if ($lines === null) {
            return back()->with('error', 'No active order found.');
        }

        $subtotal = $coupons->subtotal($lines);
        $result   = $coupons->evaluate($request->code, $subtotal, $lines);

        if (! $result['valid']) {
            return back()->with('error', $result['message'] ?? 'Invalid coupon code!');
        }

        // Only the code is remembered. The discount is recomputed on every read.
        session(['coupon' => $request->code]);

        return back()->with(
            'success',
            'Coupon applied! You saved ₹' . number_format($result['discount'], 2)
        );
    }

    private function currentOrderLines(): ?array
    {
        $sessionCode = Cookie::get('CustomerOrderId');

        if (! $sessionCode) {
            return null;
        }

        $print = DB::table('order_print')->where('customer_code', $sessionCode)->first();

        if ($print) {
            return json_decode($print->item_data, true) ?: [];
        }

        $order = DB::table('orders')->where('customer_code', $sessionCode)->first();

        return $order ? (json_decode($order->data, true) ?: []) : null;
    }

    public function removeCoupon()
    {
        session()->forget('coupon');

        return back()->with(['success' => '']);
    }

    public function checkCode(Request $request)
    {
        $restaurant_id = session()->get('restaurant_id');

        $exists = Coupon::where('code', $request->code)
            ->where('restaurant_id', $restaurant_id)
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}
