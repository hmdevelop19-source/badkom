<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PjutdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(\App\Models\Pjutd::with('badkom')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_lembaga' => 'required|unique:pjutds',
            'nama_pjutd' => 'required|string',
            'nama_madrasah' => 'nullable|string',
            'yayasan' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'badkom_id' => 'required|exists:badkoms,id',
            'id_prov' => 'nullable|integer',
            'id_kab' => 'nullable|integer',
            'id_kec' => 'nullable|integer',
            'id_kel' => 'nullable|integer',
            'alamat' => 'nullable|string',
        ]);

        $pjutd = \App\Models\Pjutd::create($validated);

        return response()->json($pjutd, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
