<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::all();
        return view('cabang.index', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_cabang' => 'required',
            'latitude'    => 'required',
            'longitude'   => 'required',
            'radius'      => 'required|numeric',
            'alamat'      => 'nullable'
        ]);

        // Mengambil data kecuali _token
        Cabang::create($request->except(['_token']));

        return redirect()->back()->with('success', 'Cabang berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_cabang' => 'required',
            'latitude'    => 'required',
            'longitude'   => 'required',
            'radius'      => 'required|numeric',
        ]);

        try {
            $cabang = Cabang::findOrFail($id);

            // Gunakan except untuk membuang _token dan _method agar tidak error di database
            $cabang->update($request->except(['_token', '_method']));

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Cabang berhasil diupdate']);
            }

            return redirect()->back()->with('success', 'Cabang berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function destroy($id)
    {
        Cabang::destroy($id);
        return redirect()->back()->with('success', 'Cabang berhasil dihapus');
    }
}
