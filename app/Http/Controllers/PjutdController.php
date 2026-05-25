<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PjutdController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = \App\Models\Pjutd::with('badkom')->orderBy('id', 'desc');

        if ($user && $user->level === 'badkom_wilayah') {
            $query->where('badkom_id', $user->badkom_id);
        }

        return response()->json($query->get());
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

        \App\Models\User::firstOrCreate(
            ['username' => $pjutd->kode_lembaga],
            [
                'fullname' => $pjutd->nama_pjutd,
                'email' => $pjutd->kode_lembaga . '@ebadkom.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'level' => 'pjutd',
                'pjutd_id' => $pjutd->id,
            ]
        );

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
                $pjutd = \App\Models\Pjutd::updateOrCreate(
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

                \App\Models\User::firstOrCreate(
                    ['username' => $pjutd->kode_lembaga],
                    [
                        'fullname' => $pjutd->nama_pjutd,
                        'email' => $pjutd->kode_lembaga . '@ebadkom.com',
                        'password' => \Illuminate\Support\Facades\Hash::make('password'),
                        'level' => 'pjutd',
                        'pjutd_id' => $pjutd->id,
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

    public function templateExcel()
    {
        $template = collect([
            ['Kode Lembaga' => '', 'Nama PJ UTD' => '', 'Nama Madrasah' => '', 'Yayasan' => '', 'Nomor HP' => '', 'Badkom ID' => '', 'ID Provinsi' => '', 'ID Kabupaten' => '', 'ID Kecamatan' => '', 'ID Kelurahan' => '', 'Alamat' => '']
        ]);
        return (new \Rap2hpoutre\FastExcel\FastExcel($template))->download('template_pjutd.xlsx');
    }

    public function exportExcel()
    {
        $pjutds = \App\Models\Pjutd::all();
        return (new \Rap2hpoutre\FastExcel\FastExcel($pjutds))->download('export_pjutd.xlsx', function ($pjutd) {
            return [
                'Kode Lembaga' => $pjutd->kode_lembaga,
                'Nama PJ UTD' => $pjutd->nama_pjutd,
                'Nama Madrasah' => $pjutd->nama_madrasah,
                'Yayasan' => $pjutd->yayasan,
                'Nomor HP' => $pjutd->no_hp,
                'Badkom ID' => $pjutd->badkom_id,
                'ID Provinsi' => $pjutd->id_prov,
                'ID Kabupaten' => $pjutd->id_kab,
                'ID Kecamatan' => $pjutd->id_kec,
                'ID Kelurahan' => $pjutd->id_kel,
                'Alamat' => $pjutd->alamat,
            ];
        });
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $collection = (new \Rap2hpoutre\FastExcel\FastExcel)->import($request->file('file'));
        $importedCount = 0;

        foreach ($collection as $row) {
            if (!empty($row['Kode Lembaga']) && !empty($row['Badkom ID'])) {
                $pjutd = \App\Models\Pjutd::updateOrCreate(
                    ['kode_lembaga' => $row['Kode Lembaga']],
                    [
                        'nama_pjutd' => $row['Nama PJ UTD'] ?? '',
                        'nama_madrasah' => $row['Nama Madrasah'] ?? null,
                        'yayasan' => $row['Yayasan'] ?? null,
                        'no_hp' => $row['Nomor HP'] ?? null,
                        'badkom_id' => $row['Badkom ID'],
                        'id_prov' => $row['ID Provinsi'] ?? null,
                        'id_kab' => $row['ID Kabupaten'] ?? null,
                        'id_kec' => $row['ID Kecamatan'] ?? null,
                        'id_kel' => $row['ID Kelurahan'] ?? null,
                        'alamat' => $row['Alamat'] ?? null,
                    ]
                );

                \App\Models\User::firstOrCreate(
                    ['username' => $pjutd->kode_lembaga],
                    [
                        'fullname' => $pjutd->nama_pjutd,
                        'email' => $pjutd->kode_lembaga . '@ebadkom.com',
                        'password' => \Illuminate\Support\Facades\Hash::make('password'),
                        'level' => 'pjutd',
                        'pjutd_id' => $pjutd->id,
                    ]
                );
                $importedCount++;
            }
        }

        return response()->json([
            'message' => "Berhasil mengimpor $importedCount data PJ UTD dari Excel."
        ]);
    }
}
