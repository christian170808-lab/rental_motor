<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses permintaan login pengguna.
     */
    public function login(Request $request)
    {
        // 1. Validasi input email dan password
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // 2. Mencoba melakukan autentikasi berdasarkan kredensial
        if (Auth::attempt($credentials)) {
            // Jika berhasil, regenerasi session untuk mencegah session fixation
            $request->session()->regenerate();
            
            // Arahkan ke dashboard dengan pesan sukses
            return redirect()->route('dashboard')->with('success', 'Selamat datang! Anda berhasil login.');
        }

        // 3. Jika gagal, kembalikan ke halaman sebelumnya dengan pesan error
        return back()->withErrors([
            'email' => 'Invalid credentials'
        ])->withInput();
    }
}