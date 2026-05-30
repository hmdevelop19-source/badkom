<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utd;
use App\Models\TahunAjaran;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

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

        $user = $request->user();

        if ($user) {
            if ($user->level === 'badkom_wilayah') {
                $query->whereHas('pjutd', function($q) use ($user) {
                    $q->where('badkom_id', $user->badkom_id);
                });
            } elseif ($user->level === 'pjutd') {
                $query->where('pjutd_id', $user->pjutd_id);
            } elseif ($user->level === 'utd') {
                $query->where('santri_id', $user->santri_id);
            }
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user && $user->level === 'badkom_wilayah') {
            return response()->json(['message' => 'Badkom Wilayah tidak diizinkan menambah penugasan.'], 403);
        }

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
        $user = $request->user();
        if ($user && $user->level === 'badkom_wilayah') {
            return response()->json(['message' => 'Badkom Wilayah tidak diizinkan mengubah penugasan.'], 403);
        }

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

    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        if ($user && $user->level === 'badkom_wilayah') {
            return response()->json(['message' => 'Badkom Wilayah tidak diizinkan menghapus penugasan.'], 403);
        }

        $utd = Utd::findOrFail($id);
        $utd->delete();

        return response()->json(['message' => 'Penugasan berhasil dihapus']);
    }

    public function cetak(Request $request)
    {
        $tahunAjaranId = $request->query('tahun_ajaran_id');
        
        $query = Utd::with(['santri.utds', 'santri.wali', 'pjutd.badkom', 'tahunAjaran'])->orderBy('id', 'asc');

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        } else {
            $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
            if ($activeTahunAjaran) {
                $query->where('tahun_ajaran_id', $activeTahunAjaran->id);
            }
        }

        $utds = $query->get();

        if ($utds->isEmpty()) {
            return response('Data penugasan tidak ditemukan untuk tahun ajaran ini.', 404);
        }

        $tahunAjaran = $utds->first()->tahunAjaran;

        // Group by Wilayah (Badkom)
        $groupedUtds = $query->get()->groupBy(function($utd) {
            return $utd->santri && $utd->santri->badkomWilayah ? $utd->santri->badkomWilayah->wilayah_koordinasi : 'Tanpa Wilayah';
        });

        // Get Kop Surat Base64
        $kopSuratPath = Setting::where('key', 'kop_surat')->value('value');
        $kopBase64 = null;
        if ($kopSuratPath && Storage::disk('local')->exists(str_replace('storage/', 'public/', $kopSuratPath))) {
            $path = Storage::disk('local')->path(str_replace('storage/', 'public/', $kopSuratPath));
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImg = file_get_contents($path);
            $kopBase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
        }

        $data = [
            'groupedUtds' => $groupedUtds,
            'tahunAjaran' => $tahunAjaran,
            'kopBase64' => $kopBase64,
        ];

        $filename = 'Validasi_Penempatan_UTD_' . str_replace(['/', '\\'], '_', $tahunAjaran->nama_tahun_ajaran) . '.pdf';
        $pdf = Pdf::loadView('pdf.penugasan_validasi', $data)->setPaper('a4', 'landscape');
        return $pdf->stream($filename);
    }
}
