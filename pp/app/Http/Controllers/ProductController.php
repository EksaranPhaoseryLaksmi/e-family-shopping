<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\VendorRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
public function index(Request $request)
{
    $query = Product::with('store');

    // Optional filter by search name
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Optional filter by vendor
    if ($request->filled('vendor_id')) {
        $query->where('vendor_id', $request->vendor_id);
    }

    // Paginate results correctly
    $products = $query->paginate(4)->withQueryString();

    return view('product.index', compact('products'));
}

   public function create($id)
{

    // Optional: validate that this vendor belongs to current user
    $vendor = VendorRequest::where('id', $id)
       // ->where('user_id', Auth::user()->id)
       ->firstOrFail();

    return view('product.create', ['vendor' => $vendor]);
}
public function store(Request $request)
{
    $request->validate([
        'vendor_request_id' => 'required|exists:vendor_requests,id',
        'name' => 'required|string',
        'description' => 'nullable|string',
        //'image.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        'variant_size.*' => 'required|string',
        'variant_quantity.*' => 'required|integer|min:0',
        'variant_price.*' => 'required|numeric|min:0',
    ]);

    $imagePaths = [];
    if ($request->hasFile('image')) {
        foreach ($request->file('image') as $image) {
            $imagePaths[] = $image->store('product_images', 'public');
        }
    }

    $variants = [];
    if ($request->has('variant_size')) {
        foreach ($request->variant_size as $index => $size) {
            if (empty($size)) continue;
            $variants[] = [
                'size' => $size,
                'quantity' => $request->variant_quantity[$index] ?? 0,
                'price' => $request->variant_price[$index] ?? 0,
            ];
        }
    }

    Product::create([
        'vendor_request_id' => $request->vendor_request_id,
        'name' => $request->name,
        'description' => $request->description,
        'image' => $imagePaths,
        'variants' => $variants,
    ]);

    return back()->with('success', 'Product created successfully with multiple variants.');
}    
    // Show edit form
public function edit($id)
{
    $product = Product::findOrFail($id);
    return view('product.edit', compact('product'));
}

// Update product
public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $vendorId = $product->vendor_request_id; 
    $request->validate([
        'name' => 'required|string',
        'description' => 'nullable|string',
        //'new_images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Decode existing images (already cast as array if $casts used)
    $existingImages = $product->image ?? [];

    // ✅ Delete selected old images
    if ($request->has('delete_images')) {
        foreach ($request->delete_images as $deletePath) {
            if (Storage::disk('public')->exists($deletePath)) {
                Storage::disk('public')->delete($deletePath);
            }

            // Remove from existing image array
            $existingImages = array_filter($existingImages, fn($img) => $img !== $deletePath);
        }
    }

    // ✅ Upload new images
    if ($request->hasFile('new_images')) {
        foreach ($request->file('new_images') as $file) {
            $existingImages[] = $file->store('product_images', 'public');
        }
    }
    // ✅ Prepare variants from inputs
    $variants = [];
    if ($request->has('variant_size')) {
        $sizes = $request->variant_size;
        $quantities = $request->variant_quantity;
        $prices = $request->variant_price;

        for ($i = 0; $i < count($sizes); $i++) {
            // Skip if any field is missing at this index
            if (!isset($sizes[$i], $quantities[$i], $prices[$i])) {
                continue;
            }

            $variants[] = [
                'size' => $sizes[$i],
                'quantity' => $quantities[$i],
                'price' => $prices[$i],
            ];
        }
    }
// ✅ Save back
    $product->update([
        'name' => $request->name,
        'description' => $request->description,
        'image' => $existingImages, // reindex
        'variants' => $variants, // your logic
    ]);
    return redirect()->route('store.products', ['vendor' => $vendorId])->with('success', 'Product updated successfully.');
}

// Delete product
public function destroy($id)
{
    $product = Product::findOrFail($id);
    $vendorId = $product->vendor_request_id; // Get vendor ID before deleting

    $product->delete();

    return redirect()->route('store.products', ['vendor' => $vendorId])
                     ->with('success', 'Product deleted!');
}

public function productsByStore(VendorRequest $vendor, Request $request)
{
    $perPage = $request->input('per_page', 4); // default 4 per page
    $search = $request->input('search');       // get search query

    $query = Product::with('store')
        ->where('vendor_request_id', $vendor->id);

    if (!empty($search)) {
        $query->where('name', 'like', '%' . $search . '%');
    }

    $products = $query->paginate($perPage)->withQueryString();

    return view('product.index', [
        'products' => $products,
        'store' => $vendor,
        'perPage' => $perPage,
        'search' => $search,
    ]);
}



public function show($id)
{
    $product = Product::with('store')->findOrFail($id);

    $variants = collect($product->variants); // Assuming variants is stored as JSON
    $sizes = $variants->pluck('size')->unique();
    $defaultVariant = $variants->first();

    // Related products: same store_type, exclude this one
    $relatedProducts = Product::where('vendor_request_id', $product->vendor_request_id)
        ->where('id', '!=', $product->id)
        ->inRandomOrder()
        ->take(4)
        ->get();

    return view('product.show', compact('product', 'variants', 'sizes', 'defaultVariant', 'relatedProducts'));
}


}

