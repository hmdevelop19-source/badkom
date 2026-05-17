<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BadkomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(\App\Models\Badkom::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_badkom' => 'required|unique:badkoms',
            'nama_pj' => 'required|string',
            'email' => 'nullable|email',
            'wilayah_koordinasi' => 'required|string',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string',
        ]);

        $badkom = \App\Models\Badkom::create($validated);

        return response()->json($badkom, 201);
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
