<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utd;
use App\Models\TahunAjaran;

class UtdController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaranId = $request->query('tahun_ajaran_id');
        
        $query = Utd::with(['santri', 'pjutd', 'tahunAjaran', 'penilaian'])->orderBy('id', 'desc');

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        } else {
            // Default to active tahun ajaran if not provided
            $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
            if ($activeTahunAjaran) {
                $query->where('tahun_ajaran_id', $activeTahunAjaran->id);
            }
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'pjutd_id' => 'required|exists:pjutds,id',
        ]);

        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        if (!$activeTahunAjaran) {
            return response()->json(['message' => 'Tidak ada Tahun Ajaran aktif. Harap buat Tahun Ajaran terlebih dahulu.'], 400);
        }

        // Check if santri is already assigned in the active year
        $exists = Utd::where('santri_id', $validated['santri_id'])
            ->where('tahun_ajaran_id', $activeTahunAjaran->id)
            ->exists();
            
        if ($exists) {
            return response()->json([
                'message' => 'Santri ini sudah ditugaskan pada tahun ajaran aktif.',
                'errors' => ['santri_id' => ['Santri ini sudah ditugaskan.']]
            ], 422);
        }

        $validated['tahun_ajaran_id'] = $activeTahunAjaran->id;
        $utd = Utd::create($validated);
        $utd->load(['santri', 'pjutd', 'tahunAjaran']);

        return response()->json($utd, 201);
    }

    public function show(string $id)
    {
        $utd = Utd::with(['santri', 'pjutd', 'tahunAjaran', 'penilaian'])->findOrFail($id);
        return response()->json($utd);
    }

    public function update(Request $request, string $id)
    {
        $utd = Utd::findOrFail($id);

        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'pjutd_id' => 'required|exists:pjutds,id',
        ]);

        // Check if santri is already assigned in this utd's year (excluding self)
        $exists = Utd::where('santri_id', $validated['santri_id'])
            ->where('tahun_ajaran_id', $utd->tahun_ajaran_id)
            ->where('id', '!=', $id)
            ->exists();
            
        if ($exists) {
            return response()->json([
                'message' => 'Santri ini sudah ditugaskan pada tahun ajaran ini.',
                'errors' => ['santri_id' => ['Santri ini sudah ditugaskan.']]
            ], 422);
        }

        $utd->update($validated);
        $utd->load(['santri', 'pjutd', 'tahunAjaran']);

        return response()->json($utd);
    }

    public function destroy(string $id)
    {
        $utd = Utd::findOrFail($id);
        $utd->delete();

        return response()->json(['message' => 'Penugasan berhasil dihapus']);
    }
}
