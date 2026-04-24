<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderController extends Controller
{

public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'cart' => 'required',
        ]);

        // 👇 Decode cart from JSON string
        $cart = json_decode($request->cart, true);

        if (!is_array($cart)) {
            return response()->json([
                'message' => 'Invalid cart format',
                'errors' => ['cart' => ['Cart data must be an array']],
            ], 422);
        }

        // 📸 Save receipt image
        $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
        $receiptNo = 'RCPT-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

        foreach ($cart as $item) {
            Order::create([
                'vendor_id'        => $item['vendor_id'] ?? null,
                'user_id'          => Auth::id(),
                'receipt_no'       => $receiptNo,
                'delivery_name'    => $request->delivery_name,
                'delivery_address' => $request->delivery_address,
                'delivery_email'   => $request->delivery_email,
                'product_name'     => $item['product_name'] ?? $item['name'] ?? 'Unknown',
                'quantity'         => $item['quantity'] ?? 1,
                'total_price'      => ($item['price'] ?? 0) * ($item['quantity'] ?? 1),
                'status'           => 'pending',
                'product_id'       => $item['id'] ?? null,
                'size'             => $item['size'] ?? 'N/A',
                'receipt_image'    => $receiptPath,
            ]);
        }

        return response()->json([
            'message'    => 'Order submitted successfully',
            'receipt_no' => $receiptNo,
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}



public function myOrders(Request $request)
{
    $query = Order::where('user_id', auth()->id())
        ->with('vendor')
        ->latest();

    // Apply filters
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('product_name', 'LIKE', "%{$search}%")
            ->orWhereHas('vendor', function ($q2) use ($search) {
                $q2->where('store_name', 'LIKE', "%{$search}%");
            });
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }


    if ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    // Get and group by receipt_no
    $orders = $query->get()->groupBy('receipt_no');

    // Pagination (manual for grouped data)
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $perPage = $request->query('perPage', 5);
    $paginatedReceipts = $orders->forPage($currentPage, $perPage)->all();

    $paginator = new LengthAwarePaginator(
        $paginatedReceipts,
        $orders->count(),
        $perPage,
        $currentPage,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    // Total summary
    $totalReceipts = $orders->count();
    $totalAmount = $orders->flatten()->sum('total_price');

    // Fetch unique shops (vendor_id and name)
    $shops = Order::where('user_id', auth()->id())
        ->with('vendor')
        ->select('vendor_id')
        ->distinct()
        ->get()
        ->map(function ($order) {
            return [
                'id' => $order->vendor_id,
                'name' => $order->vendor->store_name ?? 'Unknown',
            ];
        })
        ->unique('id')
        ->values();

    // Fetch unique product names
    $products = Order::where('user_id', auth()->id())
        ->select('product_name')
        ->distinct()
        ->pluck('product_name');

    return view('orders.history', [
        'orders' => $paginator,
        'shops' => $shops,
        'products' => $products,
        'totalReceipts' => $totalReceipts,
        'totalAmount' => $totalAmount
    ]);
}


    public function updateProduct($id,$size,$qty)
    {

        $product = Product::where('id', $id)->first();
        if ($product) {
            $updatedVariants = [];

            foreach ($product->variants as $variant) {
                if ($variant['size'] === ($size ?? 'N/A')) {
                    $variant['quantity'] = max(0, $variant['quantity'] - $qty);
                }
                $updatedVariants[] = $variant;
            }
            $product->update([
                'variants' => $updatedVariants, // your logic
                ]);
        }
    }
    public function approve(Order $order)
    {
        $order->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Order approved successfully.');
    }

    public function reject(Order $order)
    {
        $order->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Order rejected successfully.');
    }

    public function submitOrder(Request $request)
    {
        $cart = json_decode($request->cart, true);

        foreach ($cart as $item) {
            \App\Models\Order::create([
                'vendor_id' => 1,
                'user_id' => auth()->id(),

                'product_id' => $item['id'] ?? null,
                'product_name' => $item['name'],
                'size' => $item['size'] ?? null,
                'quantity' => $item['quantity'],
                'total_price' => $item['price'] * $item['quantity'],

                // ✅ REAL DATA FROM FRONTEND
                'delivery_name' => $request->delivery_name,
                'delivery_address' => $request->delivery_address,
                'delivery_email' => $request->delivery_email,

                'status' => 'paid',

                // 🔥 IMPORTANT LINK
                'receipt_no' => $request->payment_ref,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
