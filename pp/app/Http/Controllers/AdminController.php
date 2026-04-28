<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorRequest;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use App\Models\PaymentSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Optional: Add middleware to restrict access to authenticated admins
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('can:isAdmin'); // Uncomment if you have a policy or middleware for admin
    }

    // Dashboard showing all vendors and counts
    public function dashboard()
    {

         // Change this to paginate, e.g. 10 vendors per page
        $vendors = VendorRequest::paginate(10);
        $pendingCount = VendorRequest::where('status', 'pending')->count();
        $customersCount = User::where('role', 'user')->count();
       $newOrdersCount = Order::where('status', 'pending')
    ->distinct('receipt_no')
    ->count('receipt_no');
        $vendor = VendorRequest::all();
        return view('admin.dashboard', compact('vendors', 'customersCount', 'newOrdersCount', 'pendingCount','vendor'));
    }

    // Show edit form for a single vendor
    public function editVendor($id)
    {
        $vendor = VendorRequest::findOrFail($id);
        $pendingCount = VendorRequest::where('status', 'pending')->count();

        return view('admin.edit', compact('vendor', 'pendingCount'));
    }

    // Update vendor data from the edit form
    public function updateVendor(Request $request, $id)
    {
        $vendor = VendorRequest::findOrFail($id);
        // Validate all fields from the form
        $request->validate([
            'store_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'photos' => 'required|boolean',
            'delivery' => 'required|boolean',
            'payment' => 'required|string|max:255',
            'help' => 'required|boolean',
            'store_type' => 'required|integer|in:1,2,3,4',
            'status' => 'required|in:pending,approved,rejected',
        ]);
        // Update all vendor fields with proper casting for booleans
        $vendor->store_name = $request->input('store_name');
        $vendor->owner_name = $request->input('owner_name');
        $vendor->email = $request->input('email');
        $vendor->phone = $request->input('phone');
        $vendor->address = $request->input('address');
        $vendor->location = $request->input('location');
        $vendor->photos = (bool) $request->input('photos');
        $vendor->delivery = (bool) $request->input('delivery');
        $vendor->payment = $request->input('payment');
        $vendor->help = (bool) $request->input('help');
        $vendor->store_type = $request->input('store_type');
        $vendor->status = $request->input('status');
        // Save changes to DB
        $vendor->save();
        // Redirect back to dashboard with success message
        return redirect()->route('admin.dashboard')->with('success', 'Vendor updated successfully.');
    }

    // Delete a vendor
    public function deleteVendor($id)
    {
        $vendor = VendorRequest::findOrFail($id);
        $vendor->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Vendor deleted successfully.');
    }

    // Show all orders with relations loaded
    public function orders(Request $request)
    {
       $query = VendorRequest::with('user')
    ->whereHas('user', function($q) {
        $q->where('role', 'vendor');
    });
    if ($request->filled('search')) {
    $search = $request->search;
    $query->whereHas('user', function ($q) use ($search) {
        $q->where('name', 'like', "%$search%")
          ->orWhere('email', 'like', "%$search%");
    });
    }
    // Use withCount with distinct receipt_no counting per status
    $query->withCount([
    'orders as pending_orders_count' => function ($q) {
        $q->select(\DB::raw('COUNT(DISTINCT receipt_no)'))
          ->where('status', 'pending');
    },
    'orders as approved_orders_count' => function ($q) {
        $q->select(\DB::raw('COUNT(DISTINCT receipt_no)'))
          ->where('status', 'approved');
    },
    'orders as rejected_orders_count' => function ($q) {
        $q->select(\DB::raw('COUNT(DISTINCT receipt_no)'))
          ->where('status', 'rejected');
    },
    'orders as total_orders_count' => function ($q) {
        $q->select(\DB::raw('COUNT(DISTINCT receipt_no)'));
    },
    ]);
    // Paginate results
    $stores = $query->paginate(10);

    // Sum totals for current page
    $totalPending = $stores->sum('pending_orders_count');
    $totalApproved = $stores->sum('approved_orders_count');
    $totalRejected = $stores->sum('rejected_orders_count');
    $totalOrders = $stores->sum('total_orders_count');

    return view('admin.orders', compact(
        'stores', 'totalPending', 'totalApproved', 'totalRejected', 'totalOrders'
    ));
    }

    public function showOrders()
    {
    $orders = Order::with(['vendor', 'user'])->orderBy('created_at', 'desc')->get();
    $pendingCount = VendorRequest::where('status', 'pending')->count();
    return view('admin.orders', compact('orders', 'pendingCount'));
    }

    // View a single customer details
    public function viewCustomer($id)
    {
        $customer = User::findOrFail($id);
        return view('admin.customer', compact('customer'));
    }

    // Show all customers
   public function customers(Request $request)
    {
    $role = $request->query('role', 'user'); // Default to 'user' (customer)

    $customers = User::where('role', $role)->paginate(10);

    return view('admin.customers', [
        'customers' => $customers,
        'selectedRole' => $role,
        'customersCount' => User::where('role', 'user')->count(),
        'vendors' => User::where('role', 'vendor')->get()
    ]);
    }
    // Show all products

    public function products(Request $request)
    {
    $query = User::where('role', 'vendor')
        ->with(['vendor_requests.products']) // eager load stores and products
        ->withCount('vendor_requests');

    if ($request->filled('username')) {
        $query->where('name', 'like', '%' . $request->username . '%');
    }

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%');
        });
    }

    $vendors = $query->paginate(10);

    // Total vendor stores and products from current page
    $totalVendorStores = $vendors->sum('vendor_requests_count');
    $totalProducts = $vendors->flatMap(fn($user) =>
        $user->vendor_requests->flatMap(fn($store) => $store->products)
    )->count();

    return view('admin.products', compact('vendors', 'totalVendorStores','totalProducts'));
    }

    public function toggleActive(User $user)
    {
        $user->status = !$user->status; // toggle 1 <-> 0
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully.');
    }

    public function resendVerification(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'User already verified.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email resent.');
    }

    public function sendResetPassword(User $user)
    {
        Password::sendResetLink(['email' => $user->email]);

        return back()->with('success', 'Password reset link sent.');
    }

    public function index(Request $request)
    {
        $status = $request->status;

        $payments = PaymentSession::with(['user'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function check($md5)
    {
        $session = PaymentSession::where('bakong_md5', $md5)->firstOrFail();

        $response = Http::post('http://127.0.0.1:3000/check-payment', [
            'md5' => $session->bakong_md5
        ]);

        $result = $response->json();

        if (data_get($result, 'data.responseCode') === 0) {

            // already paid
            $this->markAsPaid($session);

            return back()->with('success', 'Payment verified!');
        }

        return back()->with('error', 'Still pending');
    }

    private function markAsPaid($session)
    {
        if ($session->status === 'paid') return;

        $cart = json_decode($session->cart, true);

        foreach ($cart as $item) {
            \App\Models\Order::create([
                'vendor_id' => $item['vendor_id'] ?? $session->vendor_id,
                'user_id' => $session->user_id,
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'size' => $item['size'] ?? null,
                'quantity' => $item['quantity'],
                'total_price' => $item['price'] * $item['quantity'],

                'delivery_name' => $session->delivery_name,
                'delivery_address' => $session->delivery_address,
                'delivery_email' => Auth::user()->email,

                'status' => 'paid',
                'receipt_no' => $session->payment_ref,
            ]);
        }

        $session->update(['status' => 'paid']);
    }
    public function view($ref)
    {
        $payment = \App\Models\PaymentSession::where('payment_ref', $ref)->firstOrFail();

        return view('admin.payments.view', compact('payment'));
    }
}
