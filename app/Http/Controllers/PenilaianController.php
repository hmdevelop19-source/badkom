<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilaian;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Penilaian::with([
            'utd.santri', 
            'utd.pjutd', 
            'utd.tahunAjaran',
            'utd.pjutd.badkom'
        ])->orderBy('id', 'desc');

        if ($user->level === 'badkom_wilayah') {
            $query->whereHas('utd.pjutd', function($q) use ($user) {
                $q->where('badkom_id', $user->badkom_id);
            });
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $setting = \App\Models\Setting::where('key', 'is_penilaian_opened')->first();
        if (!$setting || $setting->value !== 'true') {
            return response()->json(['message' => 'Akses penilaian saat ini sedang ditutup.'], 403);
        }

        $validated = $request->validate([
            'utd_id' => 'required|exists:utds,id',
            'keterangan' => 'required|in:Lulus,Tidak Lulus',
            'predikat' => 'required|in:A,B,C,D',
            'catatan' => 'nullable|string'
        ]);

        $penilaian = Penilaian::updateOrCreate(
            ['utd_id' => $validated['utd_id']],
            $validated
        );

        return response()->json($penilaian, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $penilaian = Penilaian::findOrFail($id);
        $user = $request->user();

        $validated = $request->validate([
            'status' => 'required|in:Menunggu,Disetujui,Ditolak',
        ]);

        if (in_array($user->level, ['admin', 'badkom_pusat'])) {
            $penilaian->status_badkom_pusat = $validated['status'];
            $penilaian->save();
            return response()->json($penilaian);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
