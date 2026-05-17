<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(\App\Models\Santri::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|unique:santris',
            'nama' => 'required',
            'nik' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'nama_ortu' => 'nullable|string',
            'nama_wali_kelas' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'email' => 'nullable|email',
            'id_prov' => 'nullable|integer',
            'id_kab' => 'nullable|integer',
            'id_kec' => 'nullable|integer',
            'id_kel' => 'nullable|integer',
        ]);

        $santri = \App\Models\Santri::create($validated);

        return response()->json($santri, 201);
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
