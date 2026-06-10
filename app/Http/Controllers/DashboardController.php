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
        $totalSantri = Santri::count();
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
        $totalSurat = SuratPermohonan::where('badkom_id', $badkomId)
            ->orWhereHas('pjutd', function($q) use ($badkomId) {
                $q->where('badkom_id', $badkomId);
            })->count();

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
        $pjutdProfile = \App\Models\Pjutd::find($pjutdId);

        $activeTahunAjaran = \App\Models\TahunAjaran::where('is_active', true)->first();
        $tahunAjaranId = $activeTahunAjaran ? $activeTahunAjaran->id : null;
        $namaTahunAjaran = $activeTahunAjaran ? $activeTahunAjaran->nama_tahun_ajaran : 'Belum diatur';

        $totalUtd = 0;
        $utdsAktif = [];
        if ($tahunAjaranId) {
            $utds = \App\Models\Utd::with('santri.user')
                ->where('pjutd_id', $pjutdId)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->where('status', 'Aktif')
                ->get();
            $totalUtd = $utds->count();
            
            $utdsAktif = $utds->map(function($utd) {
                return [
                    'id' => $utd->id,
                    'nama' => $utd->santri->nama ?? ($utd->santri->user->fullname ?? 'Tanpa Nama'),
                    'desa' => $utd->santri->desa ?? '',
                    'kecamatan' => $utd->santri->kecamatan ?? '',
                    'status' => $utd->status
                ];
            });
        }
        
        $userFilter = function($query) use ($pjutdId) {
            $query->where('pjutd_id', $pjutdId);
        };

        $totalLaporan = 0;
        if ($tahunAjaranId) {
            $totalLaporan = LaporanWajib::whereHas('user', $userFilter)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->count() 
                + LaporanMendesak::whereHas('user', $userFilter)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->count();
        } else {
            $totalLaporan = LaporanWajib::whereHas('user', $userFilter)->count() + LaporanMendesak::whereHas('user', $userFilter)->count();
        }

        $totalSurat = SuratPermohonan::where('pjutd_id', $pjutdId)->count();

        $latestLaporan = LaporanWajib::with('user')
            ->whereHas('user', $userFilter)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'role' => 'pjutd',
            'profile' => $pjutdProfile,
            'tahun_ajaran' => [
                'id' => $tahunAjaranId,
                'nama' => $namaTahunAjaran
            ],
            'utds_aktif' => $utdsAktif,
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

        $activeTahunAjaran = \App\Models\TahunAjaran::where('is_active', true)->first();
        $penugasanAktif = null;

        $laporanWajibCount = LaporanWajib::where('user_id', $user->id)
            ->when($activeTahunAjaran, function ($query) use ($activeTahunAjaran) {
                return $query->where('tahun_ajaran_id', $activeTahunAjaran->id);
            })->count();
            
        $maxBulanLaporan = \App\Models\Setting::where('key', 'max_bulan_laporan')->value('value') ?? 12;

        if ($activeTahunAjaran) {
            $penugasanAktif = \App\Models\Utd::with(['pjutd.badkom', 'tahunAjaran'])
                ->where('santri_id', $user->santri_id)
                ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                ->where('status', 'Aktif')
                ->first();
        }

        $targetTugasWajib = (int) (\App\Models\Setting::where('key', 'target_tugas_wajib')->value('value') ?? 3);
        $validLulusCount = \App\Models\Utd::where('santri_id', $user->santri_id)
            ->whereHas('penilaian', function($q) {
                $q->where('keterangan', 'Lulus');
            })->count();

        return response()->json([
            'role' => 'utd',
            'profile' => $user->load('santri.wali'),
            'penugasan_aktif' => $penugasanAktif,
            'stats' => [
                'total_laporan' => $totalLaporan,
                'laporan_wajib_count' => $laporanWajibCount,
                'max_laporan_wajib' => (int) $maxBulanLaporan,
                'target_tugas_wajib' => $targetTugasWajib,
                'valid_lulus_count' => $validLulusCount,
            ],
            'latest_laporan' => $latestLaporan,
        ]);
    }
}
