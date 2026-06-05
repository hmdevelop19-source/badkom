<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriSoal;

class KategoriSoalController extends Controller
{
    public function index(Request $request)
    {
        $target = $request->query('target_level');
        $query = KategoriSoal::with(['soalLaporan' => function($q) {
            $q->orderBy('urutan', 'asc')->orderBy('id', 'asc');
        }])->orderBy('urutan', 'asc')->orderBy('id', 'asc');
        
        if ($target) {
            $query->where('target_level', $target);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string',
            'target_level' => 'required|in:utd,pjutd',
            'urutan' => 'integer'
        ]);

        if (!isset($validated['urutan'])) {
            $maxUrutan = KategoriSoal::where('target_level', $validated['target_level'])->max('urutan');
            $validated['urutan'] = $maxUrutan ? $maxUrutan + 1 : 1;
        }

        $kategori = KategoriSoal::create($validated);
        return response()->json($kategori, 201);
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriSoal::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => 'required|string',
            'target_level' => 'required|in:utd,pjutd',
            'urutan' => 'integer'
        ]);

        $kategori->update($validated);
        return response()->json($kategori);
    }

    public function destroy($id)
    {
        $kategori = KategoriSoal::findOrFail($id);
        $kategori->delete(); // This should cascade or set null based on migration
        return response()->json(['message' => 'Kategori deleted']);
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'integer'
        ]);

        foreach ($validated['ordered_ids'] as $index => $id) {
            KategoriSoal::where('id', $id)->update(['urutan' => $index + 1]);
        }

        return response()->json(['message' => 'Reordered successfully']);
    }
}
