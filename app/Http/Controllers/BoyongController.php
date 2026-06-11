<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boyong;
use App\Models\Santri;
use App\Models\Setting;
use App\Models\Utd;
use App\Models\Penilaian;
use Illuminate\Support\Facades\DB;

class BoyongController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Boyong::class);

        $query = Boyong::with(['santri.utds.penilaian'])->orderBy('id', 'desc');

        if ($request->has('status')) {
            $query->where('status_pengajuan', $request->query('status'));
        }

        return response()->json($query->get());
    }

    public function store(\App\Http\Requests\StoreBoyongRequest $request)
    {
        $this->authorize('create', \App\Models\Boyong::class);
        $validated = $request->validated();

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
        $boyong = Boyong::findOrFail($id);
        $this->authorize('update', $boyong);

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

    public function storeManual(Request $request)
    {
        $this->authorize('create', \App\Models\Boyong::class);

        $validated = $request->validate([
            'nis' => 'required|string',
            'nama' => 'required|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'nama_wali' => 'nullable|string',
            'alamat' => 'nullable|string',
            'pjutd_data' => 'required|array|min:1',
            'pjutd_data.*.pjutd_id' => 'required|exists:pjutds,id',
            'pjutd_data.*.tahun_pendidikan' => 'required|string',
            'pjutd_data.*.nilai' => 'required|in:A,B,C,D',
            'tahun_mondok' => 'nullable|string',
            'tahun_tugas' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Cari atau buat Wali jika nama_wali ada
            $waliId = null;
            if (!empty($validated['nama_wali'])) {
                $wali = \App\Models\Wali::firstOrCreate(['nama_wali' => $validated['nama_wali']]);
                $waliId = $wali->id;
            }

            // Cari atau buat Santri
            $santri = Santri::firstOrCreate(
                ['nis' => $validated['nis']],
                [
                    'nama' => $validated['nama'],
                    'status_santri' => 'Alumni'
                ]
            );

            // Update status dan data biodata jika diperlukan
            $santri->update([
                'nama' => $validated['nama'],
                'tempat_lahir' => $validated['tempat_lahir'] ?? $santri->tempat_lahir,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? $santri->tanggal_lahir,
                'alamat' => $validated['alamat'] ?? $santri->alamat,
                'wali_id' => $waliId ?? $santri->wali_id,
                'status_santri' => 'Alumni'
            ]);

            // Buat rekam jejak UTD dan Penilaian dummy untuk setiap PJU-TD
            foreach ($validated['pjutd_data'] as $data) {
                // Find or create TahunAjaran
                $tahunAjaran = \App\Models\TahunAjaran::firstOrCreate(['nama_tahun_ajaran' => $data['tahun_pendidikan']]);

                $utd = Utd::create([
                    'santri_id' => $santri->id,
                    'pjutd_id' => $data['pjutd_id'],
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'status' => 'Selesai'
                ]);

                Penilaian::create([
                    'utd_id' => $utd->id,
                    'keterangan' => 'Lulus',
                    'predikat' => $data['nilai'],
                    'catatan' => 'Lulus Data Historis (Input Manual)',
                    'status_badkom_pusat' => 'Disetujui'
                ]);
            }

            // Buat Boyong dan SKL
            $boyong = Boyong::create([
                'santri_id' => $santri->id,
                'tahun_mondok' => $validated['tahun_mondok'] ?? null,
                'tahun_tugas' => $validated['tahun_tugas'] ?? null,
                'tanggal_pengajuan' => now(),
                'status_pengajuan' => 'Disetujui', // Langsung disetujui
                'tanggal_lulus' => now(),
                'keterangan' => 'Input Manual Alumni Lama'
            ]);

            // Generate SKL
            $noSurat = 'SKL-' . date('Ymd') . '-' . str_pad($boyong->id, 4, '0', STR_PAD_LEFT);
            $boyong->update(['no_surat' => $noSurat]);

            DB::commit();

            return response()->json([
                'message' => 'Data alumni lama berhasil ditambahkan.',
                'data' => $boyong
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menyimpan data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
