<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Welcome! Login successful.');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            /* Email tidak ditemukan — kosongkan email field, jangan withInput() */
            return back()->withErrors(['email' => 'Email not found.']);
        }

        /* Password salah — email tetap terisi, password sudah blank by default */
        return back()->withErrors(['password' => 'Incorrect password.'])->withInput(['email' => $request->email]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}