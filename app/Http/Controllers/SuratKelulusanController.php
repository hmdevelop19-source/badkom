<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SuratKelulusanController extends Controller
{
    public function cetak($id)
    {
        $santri = Santri::with([
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
            'tanggal' => Carbon::now()->translatedFormat('d F Y')
        ];

        $pdf = Pdf::loadView('pdf.surat_lulus_tugas', $data);
        return $pdf->stream('Surat_Kelulusan_Tugas_'.$santri->nis.'.pdf');
    }
}
