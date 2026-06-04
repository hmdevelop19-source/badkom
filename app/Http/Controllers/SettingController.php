<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        return response()->json(Setting::all());
    }

    public function updateBulk(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->level, ['admin', 'badkom_pusat'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        foreach ($validated['settings'] as $settingData) {
            Setting::updateOrCreate(
                ['key' => $settingData['key']],
                ['value' => $settingData['value']]
            );
        }

        return response()->json(['message' => 'Pengaturan berhasil disimpan.']);
    }

    public function uploadKop(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->level, ['admin', 'badkom_pusat'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'kop_surat' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('kop_surat')) {
            $file = $request->file('kop_surat');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('kop', $filename, 'public');

            // Save path to setting
            Setting::updateOrCreate(
                ['key' => 'kop_surat'],
                ['value' => 'storage/kop/' . $filename]
            );

            return response()->json([
                'message' => 'Kop Surat berhasil diperbarui.',
                'path' => 'storage/kop/' . $filename
            ]);
        }

        return response()->json(['message' => 'File tidak ditemukan.'], 400);
    }

    public function uploadKopLaporanUtd(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->level, ['admin', 'badkom_pusat'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'kop_laporan_utd' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('kop_laporan_utd')) {
            $file = $request->file('kop_laporan_utd');
            $filename = time() . '_utd_' . $file->getClientOriginalName();
            $path = $file->storeAs('kop', $filename, 'public');

            Setting::updateOrCreate(
                ['key' => 'kop_laporan_utd'],
                ['value' => 'storage/kop/' . $filename]
            );

            return response()->json([
                'message' => 'Kop Surat Laporan UT-D berhasil diperbarui.',
                'path' => 'storage/kop/' . $filename
            ]);
        }

        return response()->json(['message' => 'File tidak ditemukan.'], 400);
    }

    public function uploadKopLaporanPjutd(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->level, ['admin', 'badkom_pusat'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'kop_laporan_pjutd' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('kop_laporan_pjutd')) {
            $file = $request->file('kop_laporan_pjutd');
            $filename = time() . '_pjutd_' . $file->getClientOriginalName();
            $path = $file->storeAs('kop', $filename, 'public');

            Setting::updateOrCreate(
                ['key' => 'kop_laporan_pjutd'],
                ['value' => 'storage/kop/' . $filename]
            );

            return response()->json([
                'message' => 'Kop Surat Laporan PJUT-D berhasil diperbarui.',
                'path' => 'storage/kop/' . $filename
            ]);
        }

        return response()->json(['message' => 'File tidak ditemukan.'], 400);
    }
}
