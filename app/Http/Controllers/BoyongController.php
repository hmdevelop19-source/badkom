<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boyong;
use App\Models\Santri;
use App\Models\Setting;

class BoyongController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->level, ['admin', 'badkom_pusat'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Boyong::with(['santri.utds.penilaian'])->orderBy('id', 'desc');

        if ($request->has('status')) {
            $query->where('status_pengajuan', $request->query('status'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->level, ['admin', 'badkom_pusat'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'nis' => 'required|string|exists:santris,nis',
            'tahun_mondok' => 'nullable|string',
            'tahun_tugas' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ], [
            'nis.exists' => 'Santri dengan NIS tersebut tidak ditemukan.'
        ]);

        $santri = Santri::with(['utds.penilaian'])->where('nis', $validated['nis'])->firstOrFail();
        
        // Cek apakah sudah lulus tugas wajib
        $targetSetting = Setting::where('key', 'target_tugas_wajib')->first();
        $target = $targetSetting ? (int) $targetSetting->value : 3;

        $validLulusCount = $santri->utds->filter(function($utd) {
            return $utd->penilaian && 
                   $utd->penilaian->keterangan === 'Lulus';
        })->count();

        if ($validLulusCount < $target) {
            return response()->json([
                'message' => 'Tugas wajib belum selesai, tidak bisa mendapatkan izin atau surat kelulusan.'
            ], 422);
        }

        // Cek apakah sudah pernah mengajukan
        if ($santri->status_santri === 'Menunggu Boyong' || $santri->status_santri === 'Alumni') {
            return response()->json([
                'message' => 'Santri ini sudah mengajukan boyong atau sudah menjadi alumni.'
            ], 422);
        }

        $boyong = Boyong::create([
            'santri_id' => $santri->id,
            'tahun_mondok' => $validated['tahun_mondok'] ?? null,
            'tahun_tugas' => $validated['tahun_tugas'] ?? null,
            'tanggal_pengajuan' => now(),
            'status_pengajuan' => 'Menunggu',
            'keterangan' => $validated['keterangan'] ?? null
        ]);

        $santri->update(['status_santri' => 'Menunggu Boyong']);

        return response()->json([
            'message' => 'Pengajuan boyong berhasil dikirim.',
            'data' => $boyong
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !in_array($user->level, ['admin', 'badkom_pusat'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
        ]);

        $boyong = Boyong::findOrFail($id);
        
        if ($validated['status'] === 'Disetujui') {
            // Generate nomor surat kelulusan (Contoh sederhana)
            $noSurat = 'SKL-' . date('Ymd') . '-' . str_pad($boyong->id, 4, '0', STR_PAD_LEFT);
            
            $boyong->update([
                'status_pengajuan' => 'Disetujui',
                'tanggal_lulus' => now(),
                'no_surat' => $noSurat
            ]);

            $boyong->santri->update(['status_santri' => 'Alumni']);
        } else {
            $boyong->update(['status_pengajuan' => 'Ditolak']);
            $boyong->santri->update(['status_santri' => 'Aktif']);
        }

        return response()->json([
            'message' => 'Status pengajuan boyong berhasil diperbarui.',
            'data' => $boyong
        ]);
    }
}
