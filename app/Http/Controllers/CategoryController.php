<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $restaurant_id = session()->get('restaurant_id');

        $restaurants = Restaurant::all();
        $search = trim($request->search);

        $query = DB::table('categories')
            ->join('restaurants', 'restaurants.id', '=', 'categories.restaurant_id')
            ->select('categories.*', 'restaurants.restaurant_name');

        // Apply restaurant filter only if exists
        if (!empty($restaurant_id)) {
            $query->where('categories.restaurant_id', $restaurant_id);
        }

        // Apply search only if text entered
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('categories.category_name', 'LIKE', "%{$search}%")
                    ->orWhere('restaurants.restaurant_name', 'LIKE', "%{$search}%");
            });
        }

        $categories = $query->paginate(10)->withQueryString();


        return view('category.index', compact('restaurants', 'categories'));
    }
    public function store(Request $request)
    {

        $restaurant_id = session()->get('restaurant_id');

        $this->validate($request, [
            'category_name' => 'required',
            'description' => 'required',
            'image' => 'required',
            'image.*' => 'mimes:doc,docx,png,jpge,jpg',
        ]);

        $data = new Category();

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('/categories'), $filename);
            $data['image'] = $filename;
        }

        if ($restaurant_id) {
            $data->category_name = ucwords(strtolower($request->category_name));
            $data->description = $request->description;
            $data->restaurant_id = $restaurant_id;
            $data->save();
        } else {
            $data->category_name = ucwords(strtolower($request->category_name));
            $data->description = $request->description;
            $data->restaurant_id = $request->restaurantId;
            $data->save();
        }

        // return redirect()->route('category.index');
        // return redirect()->back()->with('success', 'Category added successfully!');
        return response()->json([
            'success' => true,
            'message' => 'Category added successfully!'
        ]);
    }
    public function update(Request $request, $id)
    {
        $restaurant_id = session()->get('restaurant_id');

        if ($restaurant_id) {
            $data = Category::find($id);
            $data->category_name = ucwords(strtolower($request->input('category_name')));
            $data->description = $request->input('description');
            $data->restaurant_id = $restaurant_id;

            if ($request->file('image')) {
                $file = $request->file('image');
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('/categories'), $filename);
                $data['image'] = $filename;
            }

            $data->update();
        } else {
            $data = Category::find($id);
            $data->category_name = ucwords(strtolower($request->input('category_name')));
            $data->description = $request->input('description');
            $data->restaurant_id = $request->input('restaurantId');

            if ($request->file('image')) {
                $file = $request->file('image');
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('/categories'), $filename);
                $data['image'] = $filename;
            }

            $data->update();
        }
        // return redirect()->route('category.index');
        // return redirect()->back()->with('success', 'Category updated successfully!');
        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully!'
        ]);
    }
    public function destroy(Category $category)
    {
        $category->delete();
        // return redirect()->route('category.index');
        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}
