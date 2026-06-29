<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        // Simulasi Kredensial Admin Demo
        if ($email === 'admin@smartbiz.com' && $password === 'admin123') {
            // Set session dummy untuk mensimulasikan login
            session(['logged_in' => true, 'user_name' => 'Citra Kirana']);
            
            return redirect()->route('dashboard')->with('login_success', 'Selamat datang kembali, Citra Kirana!');
        }

        // Jika salah, kembali dengan error
        return back()->withErrors([
            'email' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami.',
        ])->withInput($request->only('email'));
    }

    public function logout()
    {
        session()->forget(['logged_in', 'user_name']);
        return redirect()->route('login')->with('logout_success', 'Anda telah berhasil keluar.');
    }
}
