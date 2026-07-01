<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::with('user')->latest()->get();
        $users = User::where('status', 'AKTIF')->where('role', '!=', 'SISWA')->orderBy('name')->get();
        return view('guru.index', compact('gurus', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $created = 0;
        foreach ($request->user_ids as $userId) {
            $exists = Guru::where('user_id', $userId)->exists();
            if (!$exists) {
                $user = User::findOrFail($userId);
                Guru::create([
                    'user_id' => $userId,
                    'nama' => $user->name,
                    'nip' => $user->nip,
                    'no_hp' => $user->no_hp,
                ]);
                $user->update(['role' => 'GURU']);
                $created++;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "$created guru berhasil ditambahkan",
        ]);
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $guru->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Guru berhasil dihapus',
        ]);
    }
}
