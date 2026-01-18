<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:user,vendor',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Send verification email
        $user->sendEmailVerificationNotification();

        return redirect('/login')->with('success', 'Registered successfully. Please check your email to verify your account.');
    }


     public function login(Request $request)
     {
         $credentials = $request->validate([
             'email'    => 'required|string|email',
             'password' => 'required|string',
         ]);

         // Admin from .env (for demo or non-DB admin)
         $adminEmail = env('ADMIN_EMAIL');
         $adminPassword = env('ADMIN_PASSWORD');

         if ($credentials['email'] === $adminEmail && $credentials['password'] === $adminPassword) {
             session(['is_admin_logged_in' => true, 'admin_email' => $adminEmail]);
             return redirect()->route('admin.dashboard'); // ✅ Redirect admin
         }

         // Check user exists first
         $user = User::where('email', $credentials['email'])->first();

         if (!$user || !Hash::check($credentials['password'], $user->password)) {
             throw ValidationException::withMessages([
                 'email' => ['The provided credentials are incorrect.'],
             ]);
         }

         // ✅ Check if user is active
         if ($user->status == 0) {
             throw ValidationException::withMessages([
                 'email' => ['Your account has been disabled. Please contact admin.'],
             ]);
         }
            if (!$user->hasVerifiedEmail()) {
                throw ValidationException::withMessages([
                    'email' => ['Please verify your email before logging in.'],
                ]);
            }

         // Login the user
         Auth::login($user);
         $request->session()->regenerate();

         // Redirect based on role
         if ($user->role === 'admin') {
             return redirect()->route('admin.dashboard');
         } elseif ($user->role === 'vendor') {
             return redirect()->route('vendor.dashboard');
         } else {
             return redirect()->route('home');
         }
     }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent']);
        }

        return response()->json(['message' => 'Unable to send password reset link'], 400);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successful']);
        }

        return response()->json(['message' => 'Failed to reset password'], 400);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the CSRF token
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logged out successfully.');
    }
}
