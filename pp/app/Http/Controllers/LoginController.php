<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // After successful login, redirect based on role
    protected function authenticated(Request $request, $user)
    {
        // Check if user is active
        if ($user->status == 0) {
            Auth::logout(); // log them out just in case
            throw ValidationException::withMessages([
                'email' => ['Your account is disabled. Please contact the admin.'],
            ]);
        }

        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'seller') {
            return redirect()->route('vendor.index');
        } else {
            return redirect()->route('home');
        }
    }
}
