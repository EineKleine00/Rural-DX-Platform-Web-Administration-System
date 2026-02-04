<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Check session validity untuk multi-login prevention
     * 🔥 COMMENT DULU UNTUK TEST
     */
    protected function checkSessionValidity()
    {
        // if (Auth::check()) {
        //     $user = Auth::user();
        //     $currentSessionId = request()->session()->getId();
            
        //     // Jika session_id null atau kosong, update saja
        //     if (empty($user->session_id)) {
        //         $user->update(['session_id' => $currentSessionId]);
        //         return null;
        //     }
            
        //     // Cek apakah session masih valid
        //     if ($user->session_id !== $currentSessionId) {
        //         Auth::logout();
        //         request()->session()->invalidate();
        //         request()->session()->regenerateToken();

        //         return redirect()->route('login')
        //             ->with('error', 'Akun sedang aktif di perangkat lain.');
        //     }
        // }
        
        return null;
    }
}