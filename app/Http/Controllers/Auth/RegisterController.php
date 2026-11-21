<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Registration is intentionally limited to the very first account (the admin).
 * Once a user exists, /register redirects to login so strangers cannot sign up
 * and see the shop's sales data.
 */
class RegisterController extends Controller
{
    public function showRegister()
    {
        if (User::exists()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'রেজিস্ট্রেশন বন্ধ আছে। অনুগ্রহ করে লগইন করুন।']);
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (User::exists()) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 'password' => 'hashed' cast on the User model hashes it automatically.
        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }
}
