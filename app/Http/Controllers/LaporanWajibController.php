<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanWajibController extends Controller
{
    public function getSoal(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json([], 401);

        $kategoris = \App\Models\KategoriSoal::where('target_level', $user->level)
            ->with(['soalLaporan' => function($q) {
                $q->where('is_active', true)->orderBy('urutan', 'asc')->orderBy('id', 'asc');
            }])
            ->orderBy('urutan', 'asc')->orderBy('id', 'asc')
            ->get();
            
        return response()->json($kategoris);
    }

    public function submit(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $validated = $request->validate([
            'bulan_tahun' => 'required|string',
            'kategori_bulan' => 'required|string',
            'jawaban' => 'required|array', // key: soal_id, value: string
        ]);

        $activeTahunAjaran = \App\Models\TahunAjaran::where('is_active', true)->first();
        if (!$activeTahunAjaran) {
            return response()->json(['message' => 'Tidak ada Tahun Ajaran aktif'], 400);
        }

        $jadwal = \App\Models\JadwalLaporanWajib::where('tahun_ajaran_id', $activeTahunAjaran->id)
            ->where('kategori_bulan', $validated['kategori_bulan'])
            ->first();

        $statusWaktu = 'Tepat Waktu';
        if ($jadwal && now()->startOfDay()->greaterThan($jadwal->batas_tanggal)) {
            $statusWaktu = 'Tidak Tepat Waktu';
        }

        $laporan = \App\Models\LaporanWajib::updateOrCreate(
            [
                'user_id' => $user->id, 
                'tahun_ajaran_id' => $activeTahunAjaran->id,
                'kategori_bulan' => $validated['kategori_bulan']
            ],
            ['status' => 'submitted', 'bulan_tahun' => $validated['bulan_tahun'], 'status_waktu' => $statusWaktu]
        );

        foreach ($validated['jawaban'] as $soal_id => $jawaban_teks) {
            \App\Models\JawabanLaporan::updateOrCreate(
                ['laporan_wajib_id' => $laporan->id, 'soal_laporan_id' => $soal_id],
                ['jawaban' => $jawaban_teks]
            );
        }

        return response()->json(['message' => 'Laporan berhasil dikirim', 'data' => $laporan], 201);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = \App\Models\LaporanWajib::with(['user.santri', 'user.pjutd.utds.santri', 'user.badkom', 'jawabans.soalLaporan.kategoriSoal', 'tahunAjaran'])
            ->orderBy('id', 'desc');

        if ($user) {
            if ($user->level === 'badkom_wilayah') {
                // Can see pjutd and utd under this badkom
                $query->whereHas('user', function($q) use ($user) {
                    $q->where('badkom_id', $user->badkom_id)
                      ->orWhereHas('pjutd', function($q2) use ($user) {
                          $q2->where('badkom_id', $user->badkom_id);
                      })
                      ->orWhereHas('santri.utds.pjutd', function($q3) use ($user) {
                          $q3->where('badkom_id', $user->badkom_id);
                      });
                });
            } elseif ($user->level === 'pjutd') {
                // Can see own reports and utds under this pjutd
                $query->whereHas('user', function($q) use ($user) {
                    $q->where('id', $user->id)
                      ->orWhereHas('santri.utds', function($q2) use ($user) {
                          $q2->where('pjutd_id', $user->pjutd_id);
                      });
                });
            } elseif ($user->level === 'utd') {
                $query->where('user_id', $user->id);
            }
        }

        return response()->json($query->get());
    }
}
