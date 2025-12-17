<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function userKaryawan()
    {
        $users = User::all();
        return view('user.index', compact('users'));
    }



    // Hapus user dengan validasi role
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Cek role
        if (in_array(strtoupper($user->role), ['MANAGER', 'HR'])) {
            return response()->json([
                'success' => false,
                'message' => 'User dengan role MANAGER atau HR tidak bisa dihapus!'
            ], 403); // 403 Forbidden
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.'
        ]);
    }
}
