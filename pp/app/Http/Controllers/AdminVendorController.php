<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorRequest;
class AdminVendorController extends Controller
{
        // In AdminVendorController or AdminController

public function approve($id)
{
    $vendor = VendorRequest::findOrFail($id);
    $vendor->status = 'approved'; // ✅ Make sure it's not 'pending'
    $vendor->save();

    return redirect()->back()->with('success', 'Vendor approved successfully!');
}

    public function reject($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->status = 'rejected';
        $vendor->save();

        return redirect()->back()->with('success', 'Vendor rejected.');
    }
}
