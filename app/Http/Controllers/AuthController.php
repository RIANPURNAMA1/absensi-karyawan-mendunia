<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('Auth.login');
    }

    public function registerForm()
    {
        return view('Auth.register');
    }



    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    // PAKSA TRUE: Ini akan membuat cookie 'remember_me' aktif selamanya
    $remember = true; 

    if (!Auth::attempt($credentials, $remember)) {
        return response()->json([
            'message' => 'Email atau password salah'
        ], 401);
    }

    $user = Auth::user();

    if ($user->status !== 'AKTIF') {
        Auth::logout();
        return response()->json([
            'message' => 'Akun tidak aktif'
        ], 403);
    }

    $user->update(['last_login' => now()]);

    $redirect = match ($user->role) {
        'HR', 'MANAGER' => route('dashboard'),
        'KARYAWAN'      => route('absensi.index'),
        default         => route('login')
    };

    return response()->json([
        'message'  => 'Login berhasil',
        'redirect' => $redirect
    ]);
}


    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:HR,MANAGER,KARYAWAN',
            'cabang_id' => 'nullable|integer'
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'cabang_id' => $data['cabang_id'],
            'status' => 'AKTIF'
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil',
            'redirect' => route('login')
        ]);
    }
}
