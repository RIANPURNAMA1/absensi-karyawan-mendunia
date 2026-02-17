<?php

namespace App\Http\Controllers;

use App\Models\project_lists;
use Illuminate\Http\Request;

class ProjectListsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_list'  => 'required|string|max:50',
            'project_id' => 'required' 
        ]);

        // Gunakan nama model 'project_lists' sesuai deklarasi file modelmu
        project_lists::create([
            'project_id' => $request->project_id,
            'nama_list'  => $request->nama_list,
            'urutan'     => project_lists::where('project_id', $request->project_id)->count() + 1,
        ]);

        return redirect()->back()->with('success', 'Daftar berhasil ditambahkan!');
    }
}