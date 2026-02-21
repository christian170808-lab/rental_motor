<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses login user
     * - Validasi email & password
     * - Cek apakah email terdaftar
     * - Cek apakah password benar
     * - Regenerasi session untuk keamanan
     */
    public function login(Request $request)
    {
        // Validasi input form login
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Jika credentials benar, login berhasil
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Regenerasi session ID untuk mencegah session fixation
            return redirect()->route('dashboard')->with('success', 'Welcome! Login successful.');
        }

        // Cek apakah email ada di database
        $user = \App\Models\User::where('email', $request->email)->first();

        // Email tidak ditemukan
        if (!$user) {
            return back()->withErrors([
                'email' => 'Email not found.',
            ])->withInput();
        }

        // Email ada tapi password salah
        return back()->withErrors([
            'password' => 'Incorrect password.',
        ])->withInput();
    }

    /**
     * Logout user
     * - Hapus session
     * - Regenerasi CSRF token
     * - Redirect ke halaman login
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();      // Hapus semua data session
        $request->session()->regenerateToken(); // Buat CSRF token baru
        return redirect('/login');
    }
}