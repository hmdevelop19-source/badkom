<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utd;

class UtdController extends Controller
{
    public function index()
    {
        return response()->json(Utd::with(['santri', 'pjutd'])->orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id|unique:utds,santri_id',
            'pjutd_id' => 'required|exists:pjutds,id',
        ]);

        $utd = Utd::create($validated);
        $utd->load(['santri', 'pjutd']);

        return response()->json($utd, 201);
    }

    public function show(string $id)
    {
        $utd = Utd::with(['santri', 'pjutd'])->findOrFail($id);
        return response()->json($utd);
    }

    public function update(Request $request, string $id)
    {
        $utd = Utd::findOrFail($id);

        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id|unique:utds,santri_id,' . $id,
            'pjutd_id' => 'required|exists:pjutds,id',
        ]);

        $utd->update($validated);
        $utd->load(['santri', 'pjutd']);

        return response()->json($utd);
    }

    public function destroy(string $id)
    {
        $utd = Utd::findOrFail($id);
        $utd->delete();

        return response()->json(['message' => 'Penugasan berhasil dihapus']);
    }
}
