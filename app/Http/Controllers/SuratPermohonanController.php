<?php

namespace App\Http\Controllers;

use App\Models\SuratPermohonan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\Pjutd;

class SuratPermohonanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SuratPermohonan::with(['pjutd.badkom', 'tahunAjaran']);

        // Filter based on role
        if ($user->level === 'pjutd') {
            $query->where('pjutd_id', $user->pjutd_id);
        } elseif ($user->level === 'badkom_wilayah') {
            $query->where(function ($q) use ($user) {
                $q->where('badkom_id', $user->badkom_id)
                  ->orWhereHas('pjutd', function ($q2) use ($user) {
                      $q2->where('badkom_id', $user->badkom_id);
                  });
            });
        }

        if ($request->has('tahun_ajaran_id') && $request->tahun_ajaran_id) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'pjutd_id' => 'required_if:jenis_permohonan,Perpanjangan|nullable|exists:pjutds,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'jenis_permohonan' => 'required|in:Baru,Perpanjangan',
            'pemohon_nama' => 'required|string|max:255',
            'pemohon_umur' => 'nullable|string|max:255',
            'pemohon_jabatan' => 'nullable|string|max:255',
            'pemohon_alamat' => 'nullable|string',
            'pjutd_nama_lembaga' => 'nullable|string|max:255',
            'pjutd_alamat' => 'nullable|string',
            'pjutd_nama_kepala' => 'nullable|string|max:255',
            'pjutd_kurikulum' => 'nullable|string|max:255',
            'kriteria_ustadz' => 'required|in:diniyah_umumiyah,diniyah,umumiyah',
            'fasilitas_tempat_tinggal' => 'boolean',
            'fasilitas_kamar_mandi' => 'boolean',
            'fasilitas_wc' => 'boolean',
            'fasilitas_bisyaroh' => 'boolean',
            'fasilitas_konsumsi' => 'boolean',
        ]);

        $data = $request->all();
        // Convert boolean explicitly since inputs might be "false" string or 0
        $data['fasilitas_tempat_tinggal'] = filter_var($request->fasilitas_tempat_tinggal, FILTER_VALIDATE_BOOLEAN);
        $data['fasilitas_kamar_mandi'] = filter_var($request->fasilitas_kamar_mandi, FILTER_VALIDATE_BOOLEAN);
        $data['fasilitas_wc'] = filter_var($request->fasilitas_wc, FILTER_VALIDATE_BOOLEAN);
        $data['fasilitas_bisyaroh'] = filter_var($request->fasilitas_bisyaroh, FILTER_VALIDATE_BOOLEAN);
        $data['fasilitas_konsumsi'] = filter_var($request->fasilitas_konsumsi, FILTER_VALIDATE_BOOLEAN);

        if (Auth::user()->level === 'badkom_wilayah') {
            $data['badkom_id'] = Auth::user()->badkom_id;
        }

        $surat = SuratPermohonan::create($data);

        return response()->json([
            'message' => 'Surat permohonan berhasil dibuat',
            'data' => $surat
        ], 201);
    }

    public function show(string $id)
    {
        $surat = SuratPermohonan::with(['pjutd.badkom', 'tahunAjaran'])->findOrFail($id);
        return response()->json($surat);
    }

    public function update(Request $request, string $id)
    {
        $surat = SuratPermohonan::findOrFail($id);
        
        $data = $request->all();
        if (isset($data['fasilitas_tempat_tinggal'])) $data['fasilitas_tempat_tinggal'] = filter_var($request->fasilitas_tempat_tinggal, FILTER_VALIDATE_BOOLEAN);
        if (isset($data['fasilitas_kamar_mandi'])) $data['fasilitas_kamar_mandi'] = filter_var($request->fasilitas_kamar_mandi, FILTER_VALIDATE_BOOLEAN);
        if (isset($data['fasilitas_wc'])) $data['fasilitas_wc'] = filter_var($request->fasilitas_wc, FILTER_VALIDATE_BOOLEAN);
        if (isset($data['fasilitas_bisyaroh'])) $data['fasilitas_bisyaroh'] = filter_var($request->fasilitas_bisyaroh, FILTER_VALIDATE_BOOLEAN);
        if (isset($data['fasilitas_konsumsi'])) $data['fasilitas_konsumsi'] = filter_var($request->fasilitas_konsumsi, FILTER_VALIDATE_BOOLEAN);

        $surat->update($data);

        return response()->json([
            'message' => 'Surat permohonan berhasil diperbarui',
            'data' => $surat
        ]);
    }

    public function destroy(string $id)
    {
        $surat = SuratPermohonan::findOrFail($id);
        $surat->delete();
        
        return response()->json(['message' => 'Surat permohonan berhasil dihapus']);
    }

    public function cetak(string $id)
    {
        $surat = SuratPermohonan::with(['pjutd.badkom', 'tahunAjaran'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.surat_permohonan', compact('surat'));
        
        // Use custom page size (maybe A4 or F4), A4 is common
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('surat_permohonan_'.$id.'.pdf');
    }
}
