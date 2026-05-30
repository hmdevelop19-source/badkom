<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Badkom;
use App\Models\LaporanWajib;
use App\Models\LaporanMendesak;
use App\Models\SuratPermohonan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Base query for role-based statistics (can be expanded later)
        $totalSantri = Santri::count();
        $totalBadkom = Badkom::count();
        
        $totalLaporanWajib = LaporanWajib::count();
        $totalLaporanMendesak = LaporanMendesak::count();
        $totalLaporan = $totalLaporanWajib + $totalLaporanMendesak;
        
        $totalSurat = SuratPermohonan::count();

        // Get latest laporan wajib
        $latestLaporan = LaporanWajib::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'stats' => [
                'total_santri' => $totalSantri,
                'total_badkom' => $totalBadkom,
                'total_laporan' => $totalLaporan,
                'total_surat' => $totalSurat,
            ],
            'latest_laporan' => $latestLaporan,
        ]);
    }
}
