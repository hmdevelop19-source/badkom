<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilaian;

class PenilaianController extends Controller
{
    public function store(Request $request)
    {
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
}
