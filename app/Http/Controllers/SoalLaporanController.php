<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SoalLaporanController extends Controller
{
    public function index(Request $request)
    {
        $target = $request->query('target_level');
        $query = \App\Models\SoalLaporan::orderBy('id', 'asc');
        
        if ($target) {
            $query->where('target_level', $target);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_level' => 'required|in:utd,pjutd,badkom_wilayah',
            'pertanyaan' => 'required|string',
            'tipe_soal' => 'required|in:uraian,pilihan_ganda',
            'opsi_jawaban' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $soal = \App\Models\SoalLaporan::create($validated);
        return response()->json($soal, 201);
    }

    public function update(Request $request, $id)
    {
        $soal = \App\Models\SoalLaporan::findOrFail($id);

        $validated = $request->validate([
            'target_level' => 'required|in:utd,pjutd,badkom_wilayah',
            'pertanyaan' => 'required|string',
            'tipe_soal' => 'required|in:uraian,pilihan_ganda',
            'opsi_jawaban' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $soal->update($validated);
        return response()->json($soal);
    }

    public function destroy($id)
    {
        $soal = \App\Models\SoalLaporan::findOrFail($id);
        $soal->delete();
        return response()->json(['message' => 'Soal deleted']);
    }
}
