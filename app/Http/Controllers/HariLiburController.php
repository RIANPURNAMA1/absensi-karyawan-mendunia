<?php
namespace App\Http\Controllers;

use App\Models\HariLibur;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    public function index()
    {
        // Ambil data hari libur diurutkan dari yang terdekat
        $hariLiburs = HariLibur::orderBy('tanggal', 'asc')->get();
        return view('admin.hariLibur.index', compact('hariLiburs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:hari_liburs,tanggal',
            'keterangan' => 'required|string|max:255',
        ]);

        HariLibur::create($request->all());

        return back()->with('success', 'Hari libur berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        HariLibur::findOrFail($id)->delete();
        return back()->with('success', 'Hari libur berhasil dihapus!');
    }
}