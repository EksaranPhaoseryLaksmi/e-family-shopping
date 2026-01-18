<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\VendorRequest;

class CategoryController extends Controller
{

public function index(Request $request)
{
    $type = $request->query('type');
    $perPage = $request->query('perPage',10); // Default 6 per page
    $search = $request->query('search');

    $productsQuery = Product::with('store')
        ->whereHas('store', function ($q) use ($type) {
            $q->where('store_type', $type);
        });

    if ($search) {
        $productsQuery->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhereHas('store', function ($q2) use ($search) {
                  $q2->where('store_name', 'LIKE', "%{$search}%");
              });
        });
    }

    $products = $productsQuery->paginate($perPage)->appends($request->query());

    return view('categories', compact('products'));
}


}
