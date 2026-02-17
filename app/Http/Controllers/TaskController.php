<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{

    public function index()
    {
        // 2. Ambil data project
        $project = Projects::with(['lists.tasks'])->first();

        // 3. Ambil semua data user dari database untuk pilihan tim di modal
        $users = User::all();

        if (!$project) {
            $project = Projects::create([
                'nama_proyek' => 'Proyek Perdana',
                'manager_id' => 1
            ]);
        }

        // 4. Kirim variabel $project DAN $users ke view
        return view('task-management.index', compact('project', 'users'));
    }


    public function store(Request $request)
    {
        // Validasi dengan menangkap error-nya
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'project_list_id'   => 'required|exists:project_lists,id',
            'judul_tugas'       => 'required|string|max:255',
            'prioritas'         => 'required|in:RENDAH,SEDANG,TINGGI,DARURAT',
            'tgl_selesai_tugas' => 'nullable|date',
            'deskripsi_tugas'   => 'nullable|string',
            'user_ids'          => 'nullable|array',
            'user_ids.*'        => 'exists:users,id'
        ]);

        // JIKA GAGAL, TAMPILKAN DAFTAR DOSA INPUTNYA
        if ($validator->fails()) {
            dd($validator->errors()->all());
        }

        // Jika lolos, eksekusi simpan
        $task = \App\Models\Task::create([
            'project_list_id'   => $request->project_list_id,
            'judul_tugas'       => $request->judul_tugas,
            'deskripsi_tugas'   => $request->deskripsi_tugas,
            'prioritas'         => $request->prioritas,
            'tgl_mulai_tugas'   => now(),
            'tgl_selesai_tugas' => $request->tgl_selesai_tugas,
            'urutan_kartu'      => \App\Models\Task::where('project_list_id', $request->project_list_id)->count() + 1,
            'is_selesai'        => false,
        ]);

        if ($request->has('user_ids')) {
            $task->users()->attach($request->user_ids);
        }

        return redirect()->back()->with('success', 'Tugas berhasil dibuat!');
    }
    /**
     * Fitur Utama: Update Posisi Kartu (Drag n Drop)
     * Method ini dipanggil via AJAX dari SortableJS
     */
    public function updateOrder(Request $request)
    {
        try {
            $task = \App\Models\Task::findOrFail($request->task_id);

            // Update kolom list dan urutan
            $task->project_list_id = $request->list_id;
            $task->urutan_kartu = $request->position + 1; // +1 karena sortable mulai dari 0
            $task->save();

            // (Opsional) Rapikan urutan kartu lainnya agar tidak ada urutan ganda
            $tasks = \App\Models\Task::where('project_list_id', $request->list_id)
                ->where('id', '!=', $task->id)
                ->orderBy('urutan_kartu', 'asc')
                ->get();

            // Logika sederhana penataan ulang urutan
            // Anda bisa mengabaikan ini jika tidak ingin terlalu kompleks

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function uploadImage(Request $request)
{
    if ($request->hasFile('upload')) {
        $originName = $request->file('upload')->getClientOriginalName();
        $fileName = pathinfo($originName, PATHINFO_FILENAME);
        $extension = $request->file('upload')->getClientOriginalExtension();
        $fileName = $fileName . '_' . time() . '.' . $extension;

        // Simpan ke folder public/uploads
        $request->file('upload')->move(public_path('uploads'), $fileName);

        $url = asset('uploads/' . $fileName);

        return response()->json([
            'uploaded' => 1,
            'fileName' => $fileName,
            'url' => $url
        ]);
    }
}
}
