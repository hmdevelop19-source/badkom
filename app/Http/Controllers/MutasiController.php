<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mutasi;
use App\Models\Utd;
use App\Models\Pjutd;
use Illuminate\Support\Facades\DB;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Mutasi::with(['utd.santri', 'utd.tahunAjaran', 'asalPjutd', 'tujuanPjutd', 'user'])->orderBy('id', 'desc');

        if ($user && $user->level === 'badkom_wilayah') {
            $query->where(function($q) use ($user) {
                $q->whereHas('asalPjutd', function($q2) use ($user) {
                    $q2->where('badkom_id', $user->badkom_id);
                })->orWhereHas('tujuanPjutd', function($q2) use ($user) {
                    $q2->where('badkom_id', $user->badkom_id);
                });
            });
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'utd_id' => 'required|exists:utds,id',
            'tujuan_pjutd_id' => 'required|exists:pjutds,id',
            'alasan' => 'required|string',
            'tanggal_mutasi' => 'required|date',
        ]);

        $utd = Utd::findOrFail($validated['utd_id']);
        $asal_pjutd_id = $utd->pjutd_id;

        if ($asal_pjutd_id == $validated['tujuan_pjutd_id']) {
            return response()->json([
                'message' => 'Lembaga tujuan tidak boleh sama dengan lembaga saat ini.',
                'errors' => ['tujuan_pjutd_id' => ['Lembaga tujuan sama.']]
            ], 422);
        }

        $user = $request->user();

        // Check permissions
        if ($user->level === 'badkom_wilayah') {
            $tujuanPjutd = Pjutd::findOrFail($validated['tujuan_pjutd_id']);
            $asalPjutd = Pjutd::findOrFail($asal_pjutd_id);
            if ($asalPjutd->badkom_id !== $user->badkom_id || $tujuanPjutd->badkom_id !== $user->badkom_id) {
                return response()->json(['message' => 'Anda hanya berhak melakukan mutasi antar lembaga di dalam wilayah Anda.'], 403);
            }
        } elseif (!in_array($user->level, ['admin', 'badkom_pusat'])) {
             return response()->json(['message' => 'Anda tidak memiliki akses.'], 403);
        }

        DB::beginTransaction();
        try {
            $mutasi = Mutasi::create([
                'utd_id' => $utd->id,
                'asal_pjutd_id' => $asal_pjutd_id,
                'tujuan_pjutd_id' => $validated['tujuan_pjutd_id'],
                'alasan' => $validated['alasan'],
                'tanggal_mutasi' => $validated['tanggal_mutasi'],
                'diproses_oleh' => $user->id,
            ]);

            // Update UTD directly to new PJUTD
            $utd->update(['pjutd_id' => $validated['tujuan_pjutd_id']]);
            
            DB::commit();

            $mutasi->load(['utd.santri', 'utd.tahunAjaran', 'asalPjutd', 'tujuanPjutd', 'user']);
            return response()->json($mutasi, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan saat memproses mutasi.', 'error' => $e->getMessage()], 500);
        }
    }
}
