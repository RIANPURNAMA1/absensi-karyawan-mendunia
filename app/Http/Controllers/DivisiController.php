<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    public function index()
    {
        $divisi = Divisi::all();
        return view('divisi.index', compact('divisi'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:100|unique:divisis,nama_divisi'
        ]);

        $divisi = Divisi::create([
            'nama_divisi' => $request->nama_divisi
        ]);

        // Jika request dari AJAX
        if ($request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Divisi berhasil ditambahkan',
                'data'    => $divisi
            ], 201);
        }

        // Jika request biasa
        return redirect()
            ->back()
            ->with('success', 'Divisi berhasil ditambahkan');
    }
    public function update(Request $request, $id)
    {
        $divisi = Divisi::findOrFail($id);

        $request->validate([
            'nama_divisi' => 'required|string|max:100|unique:divisis,nama_divisi,' . $divisi->id,
        ]);

        $divisi->update([
            'nama_divisi' => $request->nama_divisi,
        ]);

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function destroy($id)
    {
        Divisi::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success'
        ]);
    }
}
