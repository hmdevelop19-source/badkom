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

    public function store(\App\Http\Requests\StoreMutasiRequest $request)
    {
        $this->authorize('create', \App\Models\Mutasi::class);
        $validated = $request->validated();

        $utd = Utd::findOrFail($validated['utd_id']);
        $asal_pjutd_id = $utd->pjutd_id;

        if ($asal_pjutd_id == $validated['tujuan_pjutd_id']) {
            return response()->json([
                'message' => 'Lembaga tujuan tidak boleh sama dengan lembaga saat ini.',
                'errors' => ['tujuan_pjutd_id' => ['Lembaga tujuan sama.']]
            ], 422);
        }

        $user = $request->user();

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

            // Update UTD old status to 'Dimutasi'
            $utd->update(['status' => 'Dimutasi']);

            // Create new UTD for the new PJU-TD
            Utd::create([
                'santri_id' => $utd->santri_id,
                'pjutd_id' => $validated['tujuan_pjutd_id'],
                'tahun_ajaran_id' => $utd->tahun_ajaran_id,
                'status' => 'Aktif'
            ]);
            
            DB::commit();

            $mutasi->load(['utd.santri', 'utd.tahunAjaran', 'asalPjutd', 'tujuanPjutd', 'user']);
            return response()->json($mutasi, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan saat memproses mutasi.', 'error' => $e->getMessage()], 500);
        }
    }
}
