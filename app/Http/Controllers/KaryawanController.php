<?php

// app/Http/Controllers/KaryawanController.php
namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::with('divisi')->get();
        $divisi   = Divisi::orderBy('nama_divisi')->get();

        return view('karyawan.index', compact('karyawan', 'divisi'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nip'           => 'required|string|max:50|unique:karyawan,nip',
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:karyawan,email|unique:users,email',
            'jabatan'       => 'required|string|max:100',
            'divisi_id'     => 'required|exists:divisis,id',
            'no_hp'         => 'required|string|max:20',
            'alamat'        => 'nullable|string',
            'tanggal_masuk' => 'required|date',
            'status_kerja'  => 'required|in:TETAP,KONTRAK,MAGANG',
            'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload foto profil
        $namaFoto = null;
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $namaFoto = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('foto-karyawan', $namaFoto, 'public');
        }

        // Gunakan transaction agar user & karyawan sinkron
        $karyawan = DB::transaction(function () use ($request, $namaFoto) {

            // Buat atau ambil user berdasarkan email
            $user = User::firstOrCreate(
                ['email' => $request->email],
                [
                    'name'     => $request->name,
                    'password' => Hash::make('12345678'), // password default
                    'role'     => 'KARYAWAN',
                    'status'   => 'AKTIF',
                ]
            );

            // Simpan data karyawan
            return Karyawan::create([
                'user_id'       => $user->id,
                'divisi_id'     => $request->divisi_id,
                'nip'           => $request->nip,
                'name'          => $request->name,
                'email'         => $request->email,
                'jabatan'       => $request->jabatan,
                'no_hp'         => $request->no_hp,
                'alamat'        => $request->alamat,
                'foto_profil'   => $namaFoto,
                'tanggal_masuk' => $request->tanggal_masuk,
                'status_kerja'  => $request->status_kerja,
            ]);
        });

        // Response JSON
        return response()->json([
            'status'  => true,
            'message' => 'Karyawan dan akun absensi berhasil dibuat',
            'akun'    => [
                'email'    => $request->email,
                'password' => '12345678'
            ],
            'data' => $karyawan
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Ambil data karyawan
        $karyawan = Karyawan::findOrFail($id);

        // Validasi
        $request->validate([
            'nip'           => 'required|unique:karyawan,nip,' . $karyawan->id,
            'name'          => 'required|string|max:255',
            'jabatan'       => 'required|string|max:255',
            'divisi_id'     => 'required|exists:divisis,id',
            'no_hp'         => 'required|string|max:20',
            'email'         => 'required|email|unique:karyawan,email|unique:users,email',
            'alamat'        => 'nullable|string',
            'tanggal_masuk' => 'required|date',
            'status_kerja'  => 'required|in:TETAP,KONTRAK,MAGANG',
            'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        /* ===============================
       HANDLE FOTO (JIKA ADA)
    =============================== */
        $namaFoto = $karyawan->foto_profil;

        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');

            $namaFoto = time() . '_' . $file->getClientOriginalName();

            // Simpan ke public/foto-karyawan
            $file->move(public_path('foto-karyawan'), $namaFoto);

            // Hapus foto lama
            if (
                $karyawan->foto_profil &&
                file_exists(public_path('foto-karyawan/' . $karyawan->foto_profil))
            ) {
                unlink(public_path('foto-karyawan/' . $karyawan->foto_profil));
            }
        }

        /* ===============================
       UPDATE DATA (AMAN & MANUAL)
    =============================== */
        $karyawan->update([
            'nip'           => $request->nip,
            'name'          => $request->name,
            'jabatan'       => $request->jabatan,
            'divisi_id'     => $request->divisi_id,
            'no_hp'         => $request->no_hp,
            'email'         => $request->email,
            'alamat'        => $request->alamat,
            'tanggal_masuk' => $request->tanggal_masuk,
            'status_kerja'  => $request->status_kerja,
            'foto_profil'   => $namaFoto,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data karyawan berhasil diperbarui',
            'data'    => $karyawan
        ]);
    }

    public function show($id)
    {
        $karyawan = Karyawan::with(['divisi', 'user'])->findOrFail($id);
        return view('karyawan.detail', compact('karyawan'));
    }




    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        // hapus foto jika ada
        if (
            $karyawan->foto_profil &&
            file_exists(public_path('foto-karyawan/' . $karyawan->foto_profil))
        ) {
            unlink(public_path('foto-karyawan/' . $karyawan->foto_profil));
        }

        $karyawan->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data karyawan berhasil dihapus'
        ]);
    }
}
