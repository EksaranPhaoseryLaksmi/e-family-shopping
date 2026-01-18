<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SellerController extends Controller
{
    // Seller dashboard
    public function dashboard()
    {
        // Logic to fetch data for seller dashboard
        return view('seller.dashboard');
    }
}
