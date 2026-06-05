<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SoalLaporanController extends Controller
{
    public function index(Request $request)
    {
        $target = $request->query('target_level');
        $query = \App\Models\SoalLaporan::with('kategoriSoal')
            ->orderBy('kategori_soal_id', 'asc')
            ->orderBy('urutan', 'asc')
            ->orderBy('id', 'asc');
        
        if ($target) {
            $query->where('target_level', $target);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        // Check if batch insert (has 'soal_list')
        if ($request->has('soal_list')) {
            $validated = $request->validate([
                'soal_list' => 'required|array',
                'soal_list.*.target_level' => 'required|in:utd,pjutd,badkom_wilayah',
                'soal_list.*.kategori_soal_id' => 'nullable|exists:kategori_soals,id',
                'soal_list.*.pertanyaan' => 'required|string',
                'soal_list.*.tipe_soal' => 'required|in:uraian,pilihan_ganda,pilihan_ganda_multi',
                'soal_list.*.opsi_jawaban' => 'nullable|array',
                'soal_list.*.is_active' => 'boolean',
                'soal_list.*.urutan' => 'nullable|integer',
            ]);

            $created = [];
            foreach ($validated['soal_list'] as $soalData) {
                if (!isset($soalData['is_active'])) $soalData['is_active'] = true;
                $created[] = \App\Models\SoalLaporan::create($soalData);
            }
            return response()->json($created, 201);
        }

        // Single insert (fallback/existing)
        $validated = $request->validate([
            'target_level' => 'required|in:utd,pjutd,badkom_wilayah',
            'kategori_soal_id' => 'nullable|exists:kategori_soals,id',
            'pertanyaan' => 'required|string',
            'tipe_soal' => 'required|in:uraian,pilihan_ganda,pilihan_ganda_multi',
            'opsi_jawaban' => 'nullable|array',
            'is_active' => 'boolean',
            'urutan' => 'nullable|integer',
        ]);

        $soal = \App\Models\SoalLaporan::create($validated);
        return response()->json($soal, 201);
    }

    public function update(Request $request, $id)
    {
        $soal = \App\Models\SoalLaporan::findOrFail($id);

        $validated = $request->validate([
            'target_level' => 'required|in:utd,pjutd,badkom_wilayah',
            'kategori_soal_id' => 'nullable|exists:kategori_soals,id',
            'pertanyaan' => 'required|string',
            'tipe_soal' => 'required|in:uraian,pilihan_ganda,pilihan_ganda_multi',
            'opsi_jawaban' => 'nullable|array',
            'is_active' => 'boolean',
            'urutan' => 'nullable|integer',
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
