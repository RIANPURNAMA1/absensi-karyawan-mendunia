<?php

namespace App\Http\Controllers;

use App\Models\PengaturanShift;
use Illuminate\Http\Request;

class PengaturanShiftController extends Controller
{
    public function index()
    {
        $mode = PengaturanShift::getMode();
        $modeLabels = [
            'fixed' => 'Shift Tetap (Default)',
            'jadwal' => 'Jadwal Shift (Per Tanggal)',
        ];

        return view('pengaturan.shift.index', compact('mode', 'modeLabels'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'shift_mode' => 'required|in:fixed,jadwal',
        ]);

        PengaturanShift::setMode($request->shift_mode);

        return redirect()->back()->with('success', 'Mode shift berhasil diperbarui');
    }
}
