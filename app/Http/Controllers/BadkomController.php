<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BadkomController extends Controller
{
    public function index()
    {
        return response()->json(\App\Models\Badkom::orderBy('id', 'desc')->get());
    }

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

    public function show(string $id)
    {
        $badkom = \App\Models\Badkom::findOrFail($id);
        return response()->json($badkom);
    }

    public function update(Request $request, string $id)
    {
        $badkom = \App\Models\Badkom::findOrFail($id);

        $validated = $request->validate([
            'kode_badkom' => 'required|unique:badkoms,kode_badkom,' . $id,
            'nama_pj' => 'required|string',
            'email' => 'nullable|email',
            'wilayah_koordinasi' => 'required|string',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string',
        ]);

        $badkom->update($validated);

        return response()->json($badkom);
    }

    public function destroy(string $id)
    {
        $badkom = \App\Models\Badkom::findOrFail($id);
        $badkom->delete();

        return response()->json(['message' => 'Badkom deleted successfully']);
    }

    public function template()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_badkom.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Kode Badkom', 'Nama PJ', 'Email', 'Wilayah Koordinasi', 'Alamat', 'Nomor HP'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=export_badkom.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $badkoms = \App\Models\Badkom::all();
        $columns = ['Kode Badkom', 'Nama PJ', 'Email', 'Wilayah Koordinasi', 'Alamat', 'Nomor HP'];

        $callback = function() use($badkoms, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($badkoms as $badkom) {
                $row = [
                    $badkom->kode_badkom,
                    $badkom->nama_pj,
                    $badkom->email,
                    $badkom->wilayah_koordinasi,
                    $badkom->alamat,
                    $badkom->no_hp,
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->path(), 'r');

        $header = fgetcsv($handle);
        $importedCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            // Mapping:
            // 0: Kode Badkom
            // 1: Nama PJ
            // 2: Email
            // 3: Wilayah Koordinasi
            // 4: Alamat
            // 5: Nomor HP

            if (!empty($row[0])) {
                \App\Models\Badkom::updateOrCreate(
                    ['kode_badkom' => $row[0]],
                    [
                        'nama_pj' => $row[1] ?? '',
                        'email' => $row[2] ?? null,
                        'wilayah_koordinasi' => $row[3] ?? '',
                        'alamat' => $row[4] ?? null,
                        'no_hp' => $row[5] ?? null,
                    ]
                );
                $importedCount++;
            }
        }

        fclose($handle);

        return response()->json([
            'message' => "Berhasil mengimpor $importedCount data Badkom."
        ]);
    }
}
