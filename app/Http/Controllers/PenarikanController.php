<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penarikan;
use App\Models\Utd;
use App\Models\Pjutd;
use App\Models\Penilaian;
use Illuminate\Support\Facades\DB;

class PenarikanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Penarikan::with(['utd.santri', 'utd.tahunAjaran', 'pjutd', 'user'])->orderBy('id', 'desc');

        if ($user && $user->level === 'badkom_wilayah') {
            $query->whereHas('pjutd', function($q) use ($user) {
                $q->where('badkom_id', $user->badkom_id);
            });
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'utd_id' => 'required|exists:utds,id',
            'alasan' => 'required|string',
            'tanggal_penarikan' => 'required|date',
        ]);

        $utd = Utd::findOrFail($validated['utd_id']);
        
        if ($utd->status !== 'Aktif') {
            return response()->json(['message' => 'UT-D ini sudah tidak aktif atau sudah ditarik.'], 422);
        }

        $pjutd_id = $utd->pjutd_id;
        $user = $request->user();

        // Check permissions
        if ($user->level === 'badkom_wilayah') {
            $pjutd = Pjutd::findOrFail($pjutd_id);
            if ($pjutd->badkom_id !== $user->badkom_id) {
                return response()->json(['message' => 'Anda hanya berhak menarik UT-D dari lembaga di wilayah Anda.'], 403);
            }
        } elseif (!in_array($user->level, ['admin', 'badkom_pusat'])) {
             return response()->json(['message' => 'Anda tidak memiliki akses.'], 403);
        }

        DB::beginTransaction();
        try {
            $penarikan = Penarikan::create([
                'utd_id' => $utd->id,
                'pjutd_id' => $pjutd_id,
                'alasan' => $validated['alasan'],
                'tanggal_penarikan' => $validated['tanggal_penarikan'],
                'diproses_oleh' => $user->id,
            ]);

            // Update UTD status to 'Ditarik'
            $utd->update(['status' => 'Ditarik']);
            
            // Create or update Penilaian to 'Tidak Tuntas'
            Penilaian::updateOrCreate(
                ['utd_id' => $utd->id],
                [
                    'predikat' => 'D', // Asumsi D = Kurang/Tidak Tuntas
                    'keterangan' => 'Tidak Tuntas',
                    'catatan' => 'Ditarik: ' . $validated['alasan']
                ]
            );
            
            DB::commit();

            $penarikan->load(['utd.santri', 'utd.tahunAjaran', 'pjutd', 'user']);
            return response()->json($penarikan, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan saat memproses penarikan.', 'error' => $e->getMessage()], 500);
        }
    }
}
