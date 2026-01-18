<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorRequest;

class CheckVendorApproved
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Please login first.');
        }

        $vendor = VendorRequest::where('user_id', Auth::id())->first();

        if (!$vendor || $vendor->status !== 'approved') {
            return redirect()->route('vendor.pending')->with('error', 'Your vendor request is not approved yet.');
        }

        return $next($request);
    }
}
