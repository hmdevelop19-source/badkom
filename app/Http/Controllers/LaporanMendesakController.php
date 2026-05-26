<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanMendesakController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = \App\Models\LaporanMendesak::with(['user.santri', 'user.pjutd', 'user.badkom', 'tahunAjaran'])
            ->orderBy('id', 'desc');

        if ($user) {
            if ($user->level === 'badkom_wilayah') {
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

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $validated = $request->validate([
            'judul' => 'required|string',
            'isi_laporan' => 'required|string',
            // optional file upload here if needed
        ]);

        $activeTahunAjaran = \App\Models\TahunAjaran::where('is_active', true)->first();
        if (!$activeTahunAjaran) {
            return response()->json(['message' => 'Tidak ada Tahun Ajaran aktif'], 400);
        }

        $laporan = \App\Models\LaporanMendesak::create([
            'user_id' => $user->id,
            'tahun_ajaran_id' => $activeTahunAjaran->id,
            'judul' => $validated['judul'],
            'isi_laporan' => $validated['isi_laporan'],
            'status_penyelesaian' => 'Menunggu',
        ]);

        return response()->json(['message' => 'Laporan mendesak berhasil dikirim', 'data' => $laporan], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $laporan = \App\Models\LaporanMendesak::findOrFail($id);
        $validated = $request->validate([
            'status_penyelesaian' => 'required|in:Menunggu,Diproses,Selesai'
        ]);

        $laporan->update($validated);
        return response()->json($laporan);
    }
}
