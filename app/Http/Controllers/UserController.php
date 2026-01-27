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


    public function index()
    {
        // Hanya ambil HR dan MANAGER
        $admins = User::whereIn('role', ['HR', 'MANAGER'])->get();
        return view('pengaturan.user.index', compact('admins'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:HR,MANAGER',
            'status' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Akun admin berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required|in:HR,MANAGER',
            'status' => 'required'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return back()->with('success', 'Data akun berhasil diperbarui');
    }

    public function destroyAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['status' => 'success']);
    }


    
}
