<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalLaporanWajib;
use App\Models\TahunAjaran;

class JadwalLaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaranId = $request->query('tahun_ajaran_id');
        
        if (!$tahunAjaranId) {
            $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
            if ($activeTahunAjaran) {
                $tahunAjaranId = $activeTahunAjaran->id;
            }
        }

        $query = JadwalLaporanWajib::query();
        
        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        return response()->json($query->get());
    }

    public function storeOrUpdate(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'jadwals' => 'required|array',
            'jadwals.*.kategori_bulan' => 'required|string',
            'jadwals.*.batas_tanggal' => 'required|date'
        ]);

        $tahunAjaranId = $validated['tahun_ajaran_id'];

        foreach ($validated['jadwals'] as $jadwalData) {
            JadwalLaporanWajib::updateOrCreate(
                [
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'kategori_bulan' => $jadwalData['kategori_bulan']
                ],
                [
                    'batas_tanggal' => $jadwalData['batas_tanggal']
                ]
            );
        }

        return response()->json(['message' => 'Jadwal laporan berhasil disimpan']);
    }
}
