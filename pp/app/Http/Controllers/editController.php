<?php

namespace App\Http\Controllers;
use App\Models\VendorRequest;

class editController extends Controller
{
public function edit($id)
{
    $vendor = VendorRequest::findOrFail($id);
    $vendors = VendorRequest::all();
    $pendingCount = VendorRequest::where('status', 'pending')->count();

    return view('admin.edit', compact('vendor', 'vendors', 'pendingCount'));
}
}
