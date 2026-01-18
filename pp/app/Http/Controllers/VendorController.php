<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class VendorController extends Controller
{
    
    // Show vendor request form
    public function create()
    {
        if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please login first.');
        }
        //$vendor = VendorRequest::where('user_id', Auth::user()->id)->first();
        //if($vendor!=null)
        //{
            //if ($vendor || $vendor->status == 'approved') 
            //{
            //return redirect()->route('store.products', ['vendor' => $vendor->id]);
        //}
    //}
    return view('vendor.create');
    }

    // Store vendor request
    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required',
            'owner_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'location' => 'required',
            'photos' => 'required|boolean',
            'delivery' => 'required|boolean',
            'payment' => 'required',
            'help' => 'required|boolean',
            'store_type' => 'required|integer',
        ]);

        $data = $request->all();

        if (Auth::check()) {
            $data['user_id'] = Auth::user()->id;
        }
        //$data['user_id'] = Auth::user()->id;
        VendorRequest::create($data);

        return redirect()->route('vendors.user');
    }

    // Vendor pending approval view
    public function thankyou()
    {
        return view('vendor.pending');
    }

    // Admin approves vendor request
    public function approve($id)
    {
        $vendor = VendorRequest::findOrFail($id);
        $vendor->status = 'approved';
        $vendor->save();

        return redirect()->route('admin.dashboard')->with('success', 'Vendor approved.');
    }

    // Admin rejects vendor request
    public function reject($id)
    {
        $vendor = VendorRequest::findOrFail($id);
        $vendor->status = 'rejected';
        $vendor->save();

        return redirect()->route('admin.dashboard')->with('error', 'Vendor rejected.');
    }

    // Vendor accesses store creation page if approved
    public function showStorePage()
    {
    // // You can still verify again for safety
    // $vendor = VendorRequest::where('user_id', Auth::id())->first();

    // if (!$vendor || $vendor->status !== 'approved') {
    //     return redirect()->route('vendor.pending')->with('error', 'You are not approved to access this page.');
    // }

    // return view('vendor.create_store', compact('vendor'));
    return view('vendor.create_store');
    }


    // AJAX: Check vendor approval status
    public function checkStatus(Request $request)
    {
        //$user = $request->user();
        //if (!$user) {
           // return response()->json(['error' => 'Unauthorized'], 401);
        //}
        $vendorRequest = VendorRequest::where('user_id', Auth::user()->id)->first();
        return response()->json([
            'approved' => $vendorRequest && $vendorRequest->status === 'approved',
        ]) ;
    }

    // Show form to edit vendor (admin)
    public function edit($id)
    {
        $vendor = VendorRequest::findOrFail($id);
        return view('admin.edit', compact('vendor'));
    }

    // Update vendor info (admin)
    public function update(Request $request, $id)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'photos' => 'required|boolean',
            'delivery' => 'required|boolean',
            'payment' => 'required|string|max:255',
            'help' => 'required|boolean',
            'store_type' => 'required|integer',
            'status' => 'required|string|in:pending,approved,rejected',
        ]);

        $vendor = VendorRequest::findOrFail($id);

        $vendor->update($request->all());

        return redirect()->route('admin.dashboard')->with('success', 'Vendor updated successfully.');
    }

    // Delete vendor (admin)
    public function destroy($id)
    {
        $vendor = VendorRequest::findOrFail($id);
        $vendor->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Vendor deleted successfully.');
    }

    // Show all approved vendors (for admin)
    public function index()
    {
        $vendors = VendorRequest::where('status', 'approved')->get();
        return view('admin.vendors', compact('vendors'));
    }
    // Show vendors owned by the currently logged-in user

    public function userVendors(Request $request)
    {
        $query = VendorRequest::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $query->where('store_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('store_type', $request->type);
        }

        $perPage = $request->input('per_page', 5); // Default: 5
        $vendors = $query->paginate($perPage)->withQueryString();

        return view('vendor.user_index', compact('vendors', 'perPage'));
    }

    public function orders(Request $request)
    {
        $vendorIds = VendorRequest::where('user_id', auth()->id())->pluck('id');
        $vendors = VendorRequest::where('user_id', auth()->id())->get();

        $filteredVendorId = $request->vendor_id;
        $filteredStatus = $request->status;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $perPage = $request->input('per_page', 5);
        $page = $request->input('page', 1);

        $ordersQuery = Order::whereIn('vendor_id', $vendorIds);

        if ($filteredVendorId) {
            $ordersQuery->where('vendor_id', $filteredVendorId);
        }

        if ($filteredStatus) {
            $ordersQuery->where('status', $filteredStatus);
        }

        if ($fromDate) {
            $ordersQuery->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $ordersQuery->whereDate('created_at', '<=', $toDate);
        }

        $ordersGrouped = $ordersQuery
            ->with('vendor')
            ->latest()
            ->get()
            ->groupBy('receipt_no');

        $paginatedGrouped = new LengthAwarePaginator(
            $ordersGrouped->forPage($page, $perPage),
            $ordersGrouped->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $totalReceipts = $ordersGrouped->count(); // $orders is grouped by receipt_no
        $totalAmount = $ordersGrouped->flatMap(fn($group) => $group)->sum('total_price');

        return view('vendor.orders', [
            'orders' => $paginatedGrouped,
            'vendors' => $vendors,
            'filteredVendorId' => $filteredVendorId,
            'filteredStatus' => $filteredStatus,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'perPage' => $perPage,
            'totalReceipts'=>$totalReceipts, 
            'totalAmount'=>$totalAmount
        ]);
    }


    public function approveReceipt($receipt_no)
    {
        // Get all vendor IDs owned by the current user
        $vendorIds = \App\Models\VendorRequest::where('user_id', auth()->id())->pluck('id');

        // Update only orders under those vendor IDs and the given receipt
        \App\Models\Order::where('receipt_no', $receipt_no)
            ->whereIn('vendor_id', $vendorIds)
            ->update(['status' => 'approved']);

        return back()->with('success', 'Receipt approved.');
    }

    public function rejectReceipt($receipt_no)
    {
        $vendorIds = \App\Models\VendorRequest::where('user_id', auth()->id())->pluck('id');

        \App\Models\Order::where('receipt_no', $receipt_no)
            ->whereIn('vendor_id', $vendorIds)
            ->update(['status' => 'rejected']);

        return back()->with('success', 'Receipt rejected.');
    }

 public function dashboard()
{
    $userId = auth()->id();
    // Get vendor IDs owned by this user
    $vendorIds = VendorRequest::where('user_id', $userId)->pluck('id');

    // 1. Vendor Stats
    $totalApprovedVendors = VendorRequest::where('user_id', $userId)->where('status', 'approved')->count();
    $totalPendingVendors = VendorRequest::where('user_id', $userId)->where('status', 'pending')->count();

    // 2. Group orders by receipt_no and status
    $orders = Order::whereIn('vendor_id', $vendorIds)->get()->groupBy('receipt_no');

    $statusCounts = [
        'approved' => 0,
        'pending' => 0,
        'rejected' => 0,
    ];

    $totalApprovedAmount = 0;

    foreach ($orders as $receiptNo => $group) {
        $status = $group->first()->status;

        if (isset($statusCounts[$status])) {
            $statusCounts[$status]++;
        }

        if ($status === 'approved') {
            $totalApprovedAmount += $group->sum('total_price');
        }
    }

    // 3. Total Products Per Vendor
    $productsPerVendor = Product::selectRaw('vendor_request_id, COUNT(*) as total_products')
        ->whereIn('vendor_request_id', $vendorIds)
        ->groupBy('vendor_request_id')
        ->get()
        ->keyBy('vendor_request_id');

    // 4. Total Orders Per Vendor by receipt_no
    $ordersPerVendor = Order::select('vendor_id', 'receipt_no')
        ->whereIn('vendor_id', $vendorIds)
        ->groupBy('vendor_id', 'receipt_no')
        ->get()
        ->groupBy('vendor_id')
        ->map(fn($group) => $group->count());

    // Pass vendor records
    $vendors = VendorRequest::where('user_id', $userId)->get();

    return view('vendor.dashboard', compact(
        'totalApprovedVendors',
        'totalPendingVendors',
        'statusCounts',
        'totalApprovedAmount',
        'productsPerVendor',
        'ordersPerVendor',
        'vendors'
    ));
}

}
