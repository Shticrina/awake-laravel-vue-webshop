<?php
namespace App\Http\Controllers;
use App\Product;
use Spatie\Searchable\Search;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductSearchController extends Controller
{
  public function index(Request $request) {

    $results = (new Search())
	    ->registerModel(Product::class, ['name'/*, 'description'*/])
	    ->search($request->input('query'));

    return response()->json($results);
  }
}