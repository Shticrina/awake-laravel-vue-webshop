<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\ProductDetails;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category'])->get();
        $products->load('details');

        $productDetails = ProductDetails::get();
        $sizes = ProductDetails::get()->unique()->pluck('size')->toArray();
        $colors = ProductDetails::get()->unique()->pluck('color')->toArray();

        foreach ($products as $product) {
            $product->colors = $product->details->pluck('color', 'color')->toArray();
            $product->sizes = $product->details->pluck('size', 'size')->toArray();
        }

        return response()->json([
            'products' => $products,
            'productDetails' => $productDetails,
            'colors' => $colors,
            'sizes' => $sizes,
            'categories' => Category::all()
        ]);
    }

    public function productsByCategory($category_id)
    {
        $products = Product::where('category_id', $category_id)->get();
        $products->load('details');

        return response()->json([
            'productsByCat' => $products
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->get('query');
        $results = Product::where('name', 'like', '%'.$search.'%')/*->orWhere('description', 'like', '%'.$search.'%')*/->get();
        $results->load('details');
        
        return $results;
    }

    public function show(Product $product, ProductDetails $details)
    {
        $product->price = $details->price;
        $product->image = $details->image;
        $product->size = $details->size;
        $product->units = $details->units;
        $product->color = $details->color;

        $product->all_colors = $details->color ? $details->all_colors() : null;
        $product->all_sizes = $details->size ? $details->all_sizes() : null;

        return response()->json($product, 200);
    }

    public function updateProductDetails(Request $request)
    {
        $no_color_message = null;
        $no_size_message = null;
        $product = Product::find($request->product_id);

        if ($request->size) {
            $search = ProductDetails::where('product_id', $request->product_id)->where('size', $request->size)->where('color', $request->product_color)->get()->first();

            if (!$search) {
                $no_color_message = 'The color '.$request->product_color.' doesn\'t exist for the chosen size!';
                $productDetails = ProductDetails::where('product_id', $request->product_id)->where('size', $request->size)->get()->first();
            } else {
                $productDetails = $search;
            }
        } 

        if ($request->color) {
            $search = ProductDetails::where('product_id', $request->product_id)->where('color', $request->color)->where('size', $request->product_size)->get()->first();

            if (!$search) {
                $no_size_message = 'The size '.$request->product_size.' doesn\'t exist for the chosen color!';
                $productDetails = ProductDetails::where('product_id', $request->product_id)->where('color', $request->color)->get()->first();
            } else {
                $productDetails = $search;
            }
        }

        $product->price = $productDetails->price;
        $product->image = $productDetails->image;
        $product->size = $productDetails->size;
        $product->units = $productDetails->units;
        $product->color = $productDetails->color;

        $product->all_colors = $productDetails->color ? $productDetails->all_colors() : null;
        $product->all_sizes = $productDetails->size ? $productDetails->all_sizes() : null;

        return response()->json([
            'product' => $product,
            'message' => $no_color_message ? $no_color_message : ($no_size_message ? $no_size_message : null)
        ]);
    }
    
    public function uploadFile(Request $request)
    {
        if ($request->hasFile('image')){
            $name = time()."_".$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $name);
        }

        return response()->json(asset("images/$name"),201);
    }

    public function edit(Product $product) // For ADMIN dashboard
    {
        $myproduct = Product::find($product->id);
        $myproduct->load('details');
        $myproduct->load('category');
        $myproduct->category_name = $myproduct->category->name;

        $myproduct->all_colors = $myproduct->details ? $myproduct->details->first()->all_colors() : null;
        $myproduct->all_sizes = $myproduct->details ? $myproduct->details->first()->all_sizes() : null;

        return response()->json($myproduct, 200);
    }
    
    public function storeOrUpdate(Request $request) // For ADMIN dashboard
    {
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'units' => $request->units,
            'price' => $request->price,
            'image' => $request->image
        ]);
        
        return response()->json([
            'status' => (bool) $product,
            'data'   => $product,
            'message' => $product ? 'Product Created!' : 'Error Creating Product'
        ]);
    }
    
    public function update(Request $request, Product $product) // For ADMIN dashboard
    {
        $status = $product->update(
            $request->only(['name', 'description', 'units', 'price', 'image'])
        );
        
        return response()->json([
            'status' => $status,
            'message' => $status ? 'Product Updated!' : 'Error Updating Product'
        ]);
    }

    public function destroy(Product $product) // For ADMIN dashboard
    {
        $status = $product->delete();
        
        return response()->json([
            'status' => $status,
            'message' => $status ? 'Product Deleted!' : 'Error Deleting Product'
        ]);
    }
}
