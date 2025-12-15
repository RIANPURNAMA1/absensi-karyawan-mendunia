<?php

// app/Http/Controllers/KaryawanController.php
namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::with('user')->get();
        return view('karyawan.index', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nip' => 'required|unique:karyawan,nip',
            'jabatan' => 'required',
            'departemen' => 'required',
            'no_hp' => 'required',
            'tanggal_masuk' => 'required|date',
            'status_kerja' => 'required|in:TETAP,KONTRAK,MAGANG'
        ]);

        $karyawan = Karyawan::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data karyawan berhasil ditambahkan',
            'karyawan' => $karyawan
        ]);
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'nip' => 'required|unique:karyawan,nip,' . $karyawan->id,
            'jabatan' => 'required',
            'departemen' => 'required',
            'no_hp' => 'required',
            'tanggal_masuk' => 'required|date',
            'status_kerja' => 'required|in:TETAP,KONTRAK,MAGANG'
        ]);

        $karyawan->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data karyawan berhasil diupdate',
            'karyawan' => $karyawan
        ]);
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data karyawan berhasil dihapus'
        ]);
    }
}
