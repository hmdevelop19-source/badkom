<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    public function index()
    {
        return response()->json(TahunAjaran::orderBy('id', 'desc')->get());
    }

    public function active()
    {
        $active = TahunAjaran::where('is_active', true)->first();
        return response()->json($active);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:50|unique:tahun_ajarans,nama_tahun_ajaran',
            'is_active' => 'boolean'
        ]);

        DB::transaction(function () use ($validated, &$tahunAjaran) {
            if (isset($validated['is_active']) && $validated['is_active']) {
                TahunAjaran::where('is_active', true)->update(['is_active' => false]);
            }
            $tahunAjaran = TahunAjaran::create($validated);
        });

        return response()->json($tahunAjaran, 201);
    }

    public function show(string $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        return response()->json($tahunAjaran);
    }

    public function update(Request $request, string $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        $validated = $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:50|unique:tahun_ajarans,nama_tahun_ajaran,' . $id,
            'is_active' => 'boolean'
        ]);

        DB::transaction(function () use ($tahunAjaran, $validated) {
            if (isset($validated['is_active']) && $validated['is_active']) {
                TahunAjaran::where('id', '!=', $tahunAjaran->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
            $tahunAjaran->update($validated);
        });

        return response()->json($tahunAjaran);
    }

    public function destroy(string $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        if ($tahunAjaran->is_active) {
            return response()->json(['message' => 'Tidak dapat menghapus tahun ajaran yang sedang aktif.'], 400);
        }
        $tahunAjaran->delete();
        return response()->json(['message' => 'Tahun Ajaran berhasil dihapus']);
    }
}
