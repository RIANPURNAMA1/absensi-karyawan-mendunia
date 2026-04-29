<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $settings = NotificationSetting::all();
        return view('pengaturan.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'array',
        ]);

        $submittedSettings = $request->settings ?? [];

        foreach (NotificationSetting::all() as $setting) {
            NotificationSetting::where('key', $setting->key)->update([
                'is_enabled' => isset($submittedSettings[$setting->key]),
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan notifikasi berhasil diperbarui.');
    }
}
