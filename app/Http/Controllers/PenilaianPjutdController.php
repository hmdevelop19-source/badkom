<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pjutd;
use App\Models\PenilaianPjutd;

class PenilaianPjutdController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tahunAjaranId = $request->query('tahun_ajaran_id');

        $query = Pjutd::with(['badkom', 'penilaianPjutds' => function ($q) use ($tahunAjaranId) {
            if ($tahunAjaranId) {
                $q->where('tahun_ajaran_id', $tahunAjaranId);
            }
        }])->orderBy('id', 'desc');

        if ($user->level === 'badkom_wilayah') {
            $query->where('badkom_id', $user->badkom_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pjutd_id' => 'required|exists:pjutds,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'predikat' => 'required|in:A,B,C,D',
            'catatan' => 'nullable|string'
        ]);

        $penilaian = PenilaianPjutd::updateOrCreate(
            [
                'pjutd_id' => $validated['pjutd_id'],
                'tahun_ajaran_id' => $validated['tahun_ajaran_id']
            ],
            $validated
        );

        return response()->json($penilaian, 201);
    }
}
