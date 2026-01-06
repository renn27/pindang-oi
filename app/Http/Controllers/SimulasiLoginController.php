<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;

class SimulasiLoginController extends Controller
{
    public function loginAs($username)
    {
        $pegawai = Pegawai::where('username', $username)->firstOrFail();
        Auth::login($pegawai); // login userp
        session()->regenerate(); // regenerasi session supaya aman
        return redirect()->route('dashboard');
    }

    public function logoutAs()
    {
        Auth::logout();
        session()->flush(); // hapus semua session
        session()->regenerate(); // regenerasi session
        return redirect()->route('dashboard');
    }
}
