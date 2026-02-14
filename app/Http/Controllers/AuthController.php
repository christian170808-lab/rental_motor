<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Memproses permintaan login pengguna
    public function login(Request $request)
    {
        // Validasi data input email dan password
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Mencoba mengautentikasi pengguna dengan kredensial yang diberikan
        if (Auth::attempt($credentials)) {
            // Regenerasi sesi untuk mencegah serangan session fixation
            $request->session()->regenerate();
            
            return redirect()->route('dashboard')->with('success', 'Welcome! You have successfully logged in.');
        }

        // Jika login gagal, kembalikan ke halaman sebelumnya dengan pesan error
        return back()->withErrors([
            'email' => 'Invalid credentials'
        ])->withInput();
    }
}