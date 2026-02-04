<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 🔥 VERSI SIMPLE: LOGIN TANPA CEK SESSION DULU
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $currentSessionId = $request->session()->getId();
            
            // Update session_id
            $user->update(['session_id' => $currentSessionId]);
            $request->session()->regenerate();

            return $this->redirectByRole($user);
        }

        return back()->withErrors([
            'login' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $user->update(['session_id' => '']);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'Logout berhasil!');
    }

    private function redirectByRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('dashboard')
                ->with('success', 'Login berhasil! Selamat datang Admin.');
        } elseif ($user->role === 'petugas') {
            return redirect()->route('surat.index')
                ->with('success', 'Login berhasil! Selamat datang Petugas.');
        } else {
            Auth::logout();
            return back()->withErrors(['role' => 'Role pengguna tidak valid.']);
        }
    }
}