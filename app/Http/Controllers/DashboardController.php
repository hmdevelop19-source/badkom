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

        switch ($user->level) {
            case 'admin':
            case 'badkom_pusat':
                return $this->getSuperadminStats();
            case 'badkom_wilayah':
                return $this->getWilayahStats($user);
            case 'pjutd':
                return $this->getPjutdStats($user);
            case 'utd':
                return $this->getUtdStats($user);
            default:
                return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    private function getSuperadminStats()
    {
        $totalSantri = \App\Models\Utd::count();
        $totalBadkom = Badkom::count();
        $totalLaporan = LaporanWajib::count() + LaporanMendesak::count();
        $totalSurat = SuratPermohonan::count();

        $latestLaporan = LaporanWajib::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'role' => 'admin',
            'stats' => [
                'total_santri' => $totalSantri, // represent UT-D on duty
                'total_badkom' => $totalBadkom,
                'total_laporan' => $totalLaporan,
                'total_surat' => $totalSurat,
            ],
            'latest_laporan' => $latestLaporan,
        ]);
    }

    private function getWilayahStats($user)
    {
        $badkomId = $user->badkom_id;

        $totalPjutd = \App\Models\Pjutd::where('badkom_id', $badkomId)->count();
        $totalUtd = \App\Models\Utd::whereHas('pjutd', function($q) use ($badkomId) {
            $q->where('badkom_id', $badkomId);
        })->count();

        // Subquery or closure to filter users under this badkom
        $userFilter = function($query) use ($badkomId) {
            $query->whereHas('pjutd', function($q) use ($badkomId) {
                $q->where('badkom_id', $badkomId);
            })->orWhere(function($q2) use ($badkomId) {
                // If the user is UTD, they have santri_id
                $q2->whereHas('santri.utds.pjutd', function($q3) use ($badkomId) {
                    $q3->where('badkom_id', $badkomId);
                });
            });
        };

        $totalLaporan = LaporanWajib::whereHas('user', $userFilter)->count() + LaporanMendesak::whereHas('user', $userFilter)->count();
        $totalSurat = SuratPermohonan::where('badkom_id', $badkomId)->count();

        $latestLaporan = LaporanWajib::with('user')
            ->whereHas('user', $userFilter)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'role' => 'badkom_wilayah',
            'stats' => [
                'total_pjutd' => $totalPjutd,
                'total_utd' => $totalUtd,
                'total_laporan' => $totalLaporan,
                'total_surat' => $totalSurat,
            ],
            'latest_laporan' => $latestLaporan,
        ]);
    }

    private function getPjutdStats($user)
    {
        $pjutdId = $user->pjutd_id;

        $totalUtd = \App\Models\Utd::where('pjutd_id', $pjutdId)->count();
        
        $userFilter = function($query) use ($pjutdId) {
            $query->where('pjutd_id', $pjutdId)
                  ->orWhereHas('santri.utds', function($q) use ($pjutdId) {
                      $q->where('pjutd_id', $pjutdId);
                  });
        };

        $totalLaporan = LaporanWajib::whereHas('user', $userFilter)->count() + LaporanMendesak::whereHas('user', $userFilter)->count();
        $totalSurat = SuratPermohonan::where('pjutd_id', $pjutdId)->count();

        $latestLaporan = LaporanWajib::with('user')
            ->whereHas('user', $userFilter)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'role' => 'pjutd',
            'stats' => [
                'total_utd' => $totalUtd,
                'total_laporan' => $totalLaporan,
                'total_surat' => $totalSurat,
            ],
            'latest_laporan' => $latestLaporan,
        ]);
    }

    private function getUtdStats($user)
    {
        $totalLaporan = LaporanWajib::where('user_id', $user->id)->count() + LaporanMendesak::where('user_id', $user->id)->count();
        
        $latestLaporan = LaporanWajib::with('user')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'role' => 'utd',
            'stats' => [
                'total_laporan' => $totalLaporan,
            ],
            'latest_laporan' => $latestLaporan,
        ]);
    }
}
