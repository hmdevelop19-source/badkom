<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PjutdController extends Controller
{
    public function index()
    {
        return response()->json(\App\Models\Pjutd::with('badkom')->orderBy('id', 'desc')->get());
    }

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

    public function show(string $id)
    {
        $pjutd = \App\Models\Pjutd::with('badkom')->findOrFail($id);
        return response()->json($pjutd);
    }

    public function update(Request $request, string $id)
    {
        $pjutd = \App\Models\Pjutd::findOrFail($id);

        $validated = $request->validate([
            'kode_lembaga' => 'required|unique:pjutds,kode_lembaga,' . $id,
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

        $pjutd->update($validated);

        return response()->json($pjutd);
    }

    public function destroy(string $id)
    {
        $pjutd = \App\Models\Pjutd::findOrFail($id);
        $pjutd->delete();

        return response()->json(['message' => 'PJ UTD deleted successfully']);
    }

    public function template()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_pjutd.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Kode Lembaga', 'Nama PJ UTD', 'Nama Madrasah', 'Yayasan', 'Nomor HP', 'Badkom ID', 'ID Provinsi', 'ID Kabupaten', 'ID Kecamatan', 'ID Kelurahan', 'Alamat'];

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
            "Content-Disposition" => "attachment; filename=export_pjutd.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $pjutds = \App\Models\Pjutd::all();
        $columns = ['Kode Lembaga', 'Nama PJ UTD', 'Nama Madrasah', 'Yayasan', 'Nomor HP', 'Badkom ID', 'ID Provinsi', 'ID Kabupaten', 'ID Kecamatan', 'ID Kelurahan', 'Alamat'];

        $callback = function() use($pjutds, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($pjutds as $pjutd) {
                $row = [
                    $pjutd->kode_lembaga,
                    $pjutd->nama_pjutd,
                    $pjutd->nama_madrasah,
                    $pjutd->yayasan,
                    $pjutd->no_hp,
                    $pjutd->badkom_id,
                    $pjutd->id_prov,
                    $pjutd->id_kab,
                    $pjutd->id_kec,
                    $pjutd->id_kel,
                    $pjutd->alamat,
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
            if (!empty($row[0]) && !empty($row[5])) { // Kode Lembaga & Badkom ID are required
                \App\Models\Pjutd::updateOrCreate(
                    ['kode_lembaga' => $row[0]],
                    [
                        'nama_pjutd' => $row[1] ?? '',
                        'nama_madrasah' => $row[2] ?? null,
                        'yayasan' => $row[3] ?? null,
                        'no_hp' => $row[4] ?? null,
                        'badkom_id' => $row[5],
                        'id_prov' => $row[6] ?? null,
                        'id_kab' => $row[7] ?? null,
                        'id_kec' => $row[8] ?? null,
                        'id_kel' => $row[9] ?? null,
                        'alamat' => $row[10] ?? null,
                    ]
                );
                $importedCount++;
            }
        }

        fclose($handle);

        return response()->json([
            'message' => "Berhasil mengimpor $importedCount data PJ UTD."
        ]);
    }
}
