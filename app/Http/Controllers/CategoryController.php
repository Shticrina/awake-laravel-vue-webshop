<?php

namespace App\Http\Controllers;

use App\Category;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Validator;

class CategoryController extends Controller
{
    public function index()
    {
        // return response()->json(Category::with(['products'])->get(),200);
        return response()->json(Category::get()->all(), 200);
    }
        
    public function edit(Category $category) // for admin dashboard
    {
        return response()->json($category, 200);
    }
    
    public function storeOrUpdate(Request $request) // for admin dashboard
    {
        if ($request->get('name') && $request->get('description')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:191|regex:/^(\w)+[a-zA-Z0-9\s.\_\-\']*(\w)+$/i',
                'description' => 'required|string|min:2|max:191|regex:/^(\w)+[a-zA-Z0-9\s.,:;!\_\-\']*(\w)+$/i'
            ])->validate();

            if ($request->get('id')) {
                $category = Category::find($request->get('id'));
            } else {
                $category = new Category;
            }

            $category->name = $request->input('name');
            $category->description = $request->input('description');
            $category->save();

            $categories = Category::get()->all();

            return response()->json([
                'categories' => $categories,
                'id' => $category->id,
                'success' => $request->get('id') ? 'Category successfully updated.' : 'Category successfully created.'
            ]);
        }

        return response()->json([
            'error' => $request->get('id') ? 'Error updating category!' : 'Error creating category!',
            'success' => false
        ]);
    }

    public function destroy(Request $request) // for admin dashboard
    {
        $category = Category::find($request->get('cat_id'));
        $status = $category->delete();

        $categories = Category::get()->all();
        
        return response()->json([
            'categories' => $categories,
            'success' => $status ? 'Category deleted.': null
        ]);
    }
}
