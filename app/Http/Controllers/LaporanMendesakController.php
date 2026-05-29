<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanMendesak;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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

    public function cetak($id)
    {
        $laporan = LaporanMendesak::with(['user.santri.utds.pjutd.badkom', 'user.pjutd.badkom'])->findOrFail($id);
        $user = $laporan->user;

        $namaPjutd = '......................................................................';
        $namaLembaga = '......................................................................';
        $alamatLembaga = '......................................................................';
        $namaUtd = '......................................................................';
        $badkomWilayah = '......................................................................';
        
        $gelarPenandatangan = 'PJ UT-D Lembaga';
        $namaPenandatangan = '...........................';
        $lokasi = '.....................';

        if ($user && $user->level === 'utd' && $user->santri) {
            $namaUtd = $user->santri->nama;
            $namaPenandatangan = $namaUtd;
            $gelarPenandatangan = 'UT-D (Santri)';
            
            // Try to find active penugasan
            $activeUtd = $user->santri->utds->first();
            if ($activeUtd && $activeUtd->pjutd) {
                $pjutd = $activeUtd->pjutd;
                $namaPjutd = $pjutd->nama_pjutd;
                $namaLembaga = $pjutd->nama_madrasah ?? $pjutd->nama_pjutd;
                $alamatLembaga = $pjutd->alamat ?? '......................................';
                $badkomWilayah = $pjutd->badkom ? $pjutd->badkom->nama_pj : '........................';
                
                // Set location from alamat if possible, or just kab
                $lokasi = 'Tempat Tugas';
            }
        } elseif ($user && $user->level === 'pjutd' && $user->pjutd) {
            $pjutd = $user->pjutd;
            $namaPjutd = $pjutd->nama_pjutd;
            $namaPenandatangan = $namaPjutd;
            $namaLembaga = $pjutd->nama_madrasah ?? $pjutd->nama_pjutd;
            $alamatLembaga = $pjutd->alamat ?? '......................................';
            $badkomWilayah = $pjutd->badkom ? $pjutd->badkom->nama_pj : '........................';
            $namaUtd = '-'; // Not specific to a single UTD usually
            $lokasi = 'Lembaga';
        }

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
            'namaPjutd' => $namaPjutd,
            'namaLembaga' => $namaLembaga,
            'alamatLembaga' => $alamatLembaga,
            'namaUtd' => $namaUtd,
            'badkomWilayah' => $badkomWilayah,
            'isiLaporan' => $laporan->isi_laporan,
            'tanggal' => Carbon::parse($laporan->created_at)->translatedFormat('d F Y'),
            'lokasi' => $lokasi,
            'gelarPenandatangan' => $gelarPenandatangan,
            'namaPenandatangan' => $namaPenandatangan,
            'kopBase64' => $kopBase64
        ];

        $pdf = Pdf::loadView('pdf.laporan_insidental', $data);
        return $pdf->stream('Laporan_Insidental_'.$laporan->id.'.pdf');
    }
}
