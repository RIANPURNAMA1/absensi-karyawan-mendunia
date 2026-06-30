<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::query();

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }

        $siswa = $query->with(['shift', 'kelasRelasi'])->latest()->get();
        $kelasList = Kelas::aktif()->get();
        $shifts = Shift::aktif()->get();

        return view('siswa.index', compact('siswa', 'kelasList', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kelas_id' => 'nullable|exists:kelas,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $foto = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $foto = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/siswa'), $foto);
        }

        $siswa = Siswa::create([
            'nama' => $request->nama,
            'kelas_id' => $request->kelas_id,
            'shift_id' => $request->shift_id,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'agama' => $request->agama,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'foto' => $foto,
            'status' => 'AKTIF',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data siswa berhasil ditambahkan',
            'data' => $siswa,
        ]);
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'kelas_id' => 'nullable|exists:kelas,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            if ($siswa->foto && file_exists(public_path('uploads/siswa/' . $siswa->foto))) {
                unlink(public_path('uploads/siswa/' . $siswa->foto));
            }
            $file = $request->file('foto');
            $foto = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/siswa'), $foto);
            $data['foto'] = $foto;
        }

        $siswa->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data siswa berhasil diperbarui',
            'data' => $siswa,
        ]);
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->foto && file_exists(public_path('uploads/siswa/' . $siswa->foto))) {
            unlink(public_path('uploads/siswa/' . $siswa->foto));
        }

        $siswa->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data siswa berhasil dihapus',
        ]);
    }

    public function toggleStatus($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->status = $siswa->status === 'AKTIF' ? 'NONAKTIF' : 'AKTIF';
        $siswa->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status siswa berhasil diubah menjadi ' . $siswa->status,
        ]);
    }

    public function buatkanAkun(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa ini sudah memiliki akun login',
            ], 422);
        }

        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $siswa->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'SISWA',
            'status' => 'AKTIF',
        ]);

        $siswa->update(['user_id' => $user->id]);

        return response()->json([
            'status' => 'success',
            'message' => 'Akun login berhasil dibuat',
            'data' => [
                'email' => $request->email,
                'password' => $request->password,
            ],
        ]);
    }
}
