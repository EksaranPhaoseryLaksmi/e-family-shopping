<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\PaymentController;


//Customer Routes
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/', fn() => view('home'))->name('home');
    Route::get('/home', fn() => view('home'))->name('home.page');
    // Categories page
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Payment page
    Route::get('/payment', function () {return view('payment');})->name('payment.page');
    // Order submission route (already exists)
    Route::post('/submit-order', [OrderController::class, 'store'])->name('order.submit');
    Route::get('/about-us', function () {
        return view('home'); // Render home view
    })->name('about.us');
    Route::get('/templates', function () {
        return view('home'); // Render home view
    })->name('templates.page');
    Route::get('/feedback', function () {
        return view('home'); // Render home view
    })->name('feedback.page');
    Route::get('/contact', function () {
        return view('home'); // Render home view
    })->name('contact.page');
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.history');
    Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/payment', [PaymentController::class, 'index']);
    // ✅ Generate QR
    //Route::post('/generate-khqr', [PaymentController::class, 'generateQr']);
    // ✅ Check payment
    Route::get('/check-payment', [PaymentController::class, 'checkQr']);
    // ✅ Your existing order
    Route::post('/submit-order', [PaymentController::class, 'submitOrder']);
   Route::get('/payment-qr', [PaymentController::class, 'showPage']);
   //Route::post('/khqr/generate', [PaymentController::class, 'generateQr']);
   Route::post('/khqr/generate', [PaymentController::class, 'generateKHQR']);
   Route::get('/khqr/check', [PaymentController::class, 'checkPayment']);

});

Route::middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/vendor/register', [VendorController::class, 'create'])->name('vendor.create');
    Route::post('/vendor/register', [VendorController::class, 'store'])->name('vendor.store');
    Route::get('/vendor/pending', [VendorController::class, 'thankyou'])->name('vendor.pending');
    Route::get('/vendor/status/check', [VendorController::class, 'checkStatus'])->name('vendor.checkStatus');
    Route::get('/admin/vendors', [VendorController::class, 'index'])->name('admin.vendors.index');
     Route::get('/products', [ProductController::class, 'index'])->name('product.index');
    Route::get('/product/create/{id}', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::put('/product/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::get('/store/{vendor}', [ProductController::class, 'productsByStore'])->name('store.products');
    Route::get('/my-vendors', [VendorController::class, 'userVendors'])->name('vendors.user');
    Route::middleware(['auth', 'vendor.approved'])->group(function () {
        Route::get('/vendor/create_store', [VendorController::class, 'showStorePage'])->name('vendor.create_store');
    });
     Route::get('/vendor/orders', [VendorController::class, 'orders'])->name('vendors.orders');
     Route::post('/vendor/orders/approve-receipt/{receipt_no}', [VendorController::class, 'approveReceipt'])->name('vendor.orders.approveReceipt');
    Route::post('/vendor/orders/reject-receipt/{receipt_no}', [VendorController::class, 'rejectReceipt'])->name('vendor.orders.rejectReceipt');
    Route::get('/vendor/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard');
});
// Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/categories', function () {
    return view('categories'); // because your file is in views/, not views/admin/
    })->name('admin.categories');
    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
    // Customer routes
    Route::get('/admin/customer/{id}', [AdminController::class, 'viewCustomer'])->name('admin.viewCustomer');
    Route::get('/admin/customers', [\App\Http\Controllers\AdminController::class, 'customers'])->name('admin.customers');
    Route::get('/admin/products', [\App\Http\Controllers\AdminController::class, 'products'])->name('admin.products');
    // Vendor approval actions
    Route::post('/admin/vendors/{vendor}/approve', [VendorController::class, 'approve'])->name('admin.vendors.approve');
    Route::post('/admin/vendors/{vendor}/reject', [VendorController::class, 'reject'])->name('admin.vendors.reject');
    // Vendor CRUD actions for admin
    Route::get('/admin/vendors/{vendor}/edit', [AdminController::class, 'editVendor'])->name('admin.vendors.edit');
    Route::put('/admin/vendors/{vendor}', [AdminController::class, 'updateVendor'])->name('admin.vendors.update');
    Route::delete('/admin/vendors/{vendor}', [VendorController::class, 'destroy'])->name('admin.vendors.delete');
    Route::post('/admin/orders/{order}/approve', [OrderController::class, 'approve'])->name('admin.orders.approve');
    Route::post('/admin/orders/{order}/reject', [OrderController::class, 'reject'])->name('admin.orders.reject');
    Route::post('/admin/users/{user}/toggle', [AdminController::class, 'toggleActive'])->name('admin.users.toggle');
    Route::get('/payments', [AdminController::class, 'index'])->name('admin.payments');
    Route::post('/payments/{id}/check', [AdminController::class, 'check']);
    Route::get('/payments/{ref}', [AdminController::class, 'view'])
            ->name('admin.payments.view');
});

// Auth routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
// Show verification notice
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {

    $user = User::findOrFail($id);

    // Validate hash
    if (! hash_equals(
        sha1($user->getEmailForVerification()),
        $hash
    )) {
        abort(403, 'Invalid verification link.');
    }

    // Already verified
    if ($user->hasVerifiedEmail()) {
        return redirect('/login')->with('info', 'Email already verified.');
    }

    // Mark email as verified
    $user->markEmailAsVerified();

    return redirect('/login')->with('success', 'Email verified successfully. Please login.');
})
->middleware(['signed'])
->name('verification.verify');

// Handle verification link
// Temporarily remove auth middleware for testing only
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// Resend verification email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::post('/admin/users/{user}/resend-verification', [AdminController::class, 'resendVerification'])
    ->name('admin.users.resendVerification');

Route::post('/admin/users/{user}/reset-password', [AdminController::class, 'sendResetPassword'])
    ->name('admin.users.resetPassword');

// Show forgot password form
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

// Send reset link email
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

// Show reset password form
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

// Handle password reset
Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');
