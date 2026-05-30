<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SuratKelulusanController extends Controller
{
    public function cetak($id)
    {
        $santri = Santri::with([
            'wali',
            'boyong',
            'utds.pjutd', 
            'utds.tahunAjaran', 
            'utds.penilaian'
        ])->findOrFail($id);

        $settingsData = Setting::pluck('value', 'key');
        $targetWajib = intval($settingsData['target_tugas_wajib'] ?? 3);
        $namaKoordinator = $settingsData['nama_koordinator_tugas'] ?? 'SAIFUL BARI';

        // Calculate age
        $umur = '';
        if ($santri->tanggal_lahir) {
            $umur = Carbon::parse($santri->tanggal_lahir)->age . ' Tahun';
        }

        // Get all penugasan
        $allTugas = $santri->utds()->with(['pjutd', 'tahunAjaran', 'penilaian'])->get();
        
        $validTugas = $allTugas->filter(function($utd) {
            return $utd->penilaian != null && $utd->penilaian->keterangan === 'Lulus';
        });

        $countValid = $validTugas->count();
        $wajibCount = min($countValid, $targetWajib);
        $tathowwuCount = max(0, $countValid - $targetWajib);

        $predikatAkhir = 'B'; // Default fallback
        if ($validTugas->isNotEmpty()) {
            // Very simple logic: get the last valid tugas predikat
            $lastValid = $validTugas->last();
            $predikatAkhir = $lastValid->penilaian->predikat;
        }

        $keteranganPredikat = match ($predikatAkhir) {
            'A' => 'SANGAT BAIK',
            'B' => 'BAIK',
            'C' => 'CUKUP',
            'D' => 'KURANG',
            default => 'BAIK',
        };

        // Determine address
        $alamatLengkap = $santri->alamat;
        // Ideally append kel/kec/kab if relations exist, but for now just use alamat field.

        // Get Kop Surat Base64
        $kopSuratPath = Setting::where('key', 'kop_surat')->value('value');
        $kopBase64 = null;
        if ($kopSuratPath) {
            $relativePath = str_replace('storage/', '', $kopSuratPath);
            if (Storage::disk('public')->exists($relativePath)) {
                $path = Storage::disk('public')->path($relativePath);
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $dataImg = file_get_contents($path);
                $kopBase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
            }
        }

        $data = [
            'santri' => $santri,
            'umur' => $umur,
            'allTugas' => $allTugas,
            'wajibCount' => $wajibCount,
            'tathowwuCount' => $tathowwuCount,
            'predikatAkhir' => $predikatAkhir,
            'keteranganPredikat' => $keteranganPredikat,
            'namaKoordinator' => $namaKoordinator,
            'alamatLengkap' => $alamatLengkap,
            'tanggal' => Carbon::now()->translatedFormat('d F Y'),
            'kopBase64' => $kopBase64,
            'tahunMondok' => $santri->boyong->tahun_mondok ?? '........................',
            'tahunTugas' => $santri->boyong->tahun_tugas ?? '........................'
        ];

        $pdf = Pdf::loadView('pdf.surat_lulus_tugas', $data);
        return $pdf->stream('Surat_Kelulusan_Tugas_'.$santri->nis.'.pdf');
    }
}
