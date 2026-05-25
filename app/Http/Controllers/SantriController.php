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
            'id_prov' => 'nullable|integer',
            'id_kab' => 'nullable|integer',
            'id_kec' => 'nullable|integer',
            'id_kel' => 'nullable|integer',
            
            'nik_wali' => 'nullable|string',
            'nama_wali' => 'nullable|string',
            'no_hp_wali' => 'nullable|string',
            'email_wali' => 'nullable|email',
        ]);

        $wali = null;
        if (!empty($validated['nik_wali']) || !empty($validated['nama_wali'])) {
            $wali = \App\Models\Wali::firstOrCreate(
                ['nik' => $validated['nik_wali'] ?? null],
                [
                    'nama_wali' => $validated['nama_wali'] ?? 'Tanpa Nama',
                    'no_hp' => $validated['no_hp_wali'] ?? null,
                    'email' => $validated['email_wali'] ?? null,
                    'id_prov' => $validated['id_prov'] ?? null,
                    'id_kab' => $validated['id_kab'] ?? null,
                    'id_kec' => $validated['id_kec'] ?? null,
                    'id_kel' => $validated['id_kel'] ?? null,
                    'alamat' => $validated['alamat'] ?? null,
                ]
            );
        }

        $santriData = collect($validated)->except(['nik_wali', 'nama_wali', 'no_hp_wali', 'email_wali'])->toArray();
        if ($wali) {
            $santriData['wali_id'] = $wali->id;
        }

        $santri = \App\Models\Santri::create($santriData);

        return response()->json($santri, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $santri = \App\Models\Santri::with(['utds.pjutd', 'utds.tahunAjaran', 'utds.penilaian', 'wali'])->findOrFail($id);
        return response()->json($santri);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $santri = \App\Models\Santri::findOrFail($id);

        $validated = $request->validate([
            'nis' => 'required|unique:santris,nis,' . $id,
            'nama' => 'required',
            'nik' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'id_prov' => 'nullable|integer',
            'id_kab' => 'nullable|integer',
            'id_kec' => 'nullable|integer',
            'id_kel' => 'nullable|integer',

            'nik_wali' => 'nullable|string',
            'nama_wali' => 'nullable|string',
            'no_hp_wali' => 'nullable|string',
            'email_wali' => 'nullable|email',
        ]);

        $wali = null;
        if (!empty($validated['nik_wali']) || !empty($validated['nama_wali'])) {
            $wali = \App\Models\Wali::updateOrCreate(
                ['nik' => $validated['nik_wali'] ?? null],
                [
                    'nama_wali' => $validated['nama_wali'] ?? 'Tanpa Nama',
                    'no_hp' => $validated['no_hp_wali'] ?? null,
                    'email' => $validated['email_wali'] ?? null,
                    'id_prov' => $validated['id_prov'] ?? null,
                    'id_kab' => $validated['id_kab'] ?? null,
                    'id_kec' => $validated['id_kec'] ?? null,
                    'id_kel' => $validated['id_kel'] ?? null,
                    'alamat' => $validated['alamat'] ?? null,
                ]
            );
        }

        $santriData = collect($validated)->except(['nik_wali', 'nama_wali', 'no_hp_wali', 'email_wali'])->toArray();
        if ($wali) {
            $santriData['wali_id'] = $wali->id;
        }

        $santri->update($santriData);

        return response()->json($santri);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $santri = \App\Models\Santri::findOrFail($id);
        $santri->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function export()
    {
        $santris = \App\Models\Santri::all();
        $filename = "santri_export.csv";
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        return response()->stream(function() use ($santris) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['nis', 'nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'nik_wali', 'nama_wali', 'no_hp_wali', 'email_wali']);
            foreach ($santris as $santri) {
                fputcsv($handle, [
                    $santri->nis, $santri->nama, $santri->nik, $santri->jenis_kelamin, 
                    $santri->tempat_lahir, $santri->tanggal_lahir, $santri->alamat, 
                    $santri->wali ? $santri->wali->nik : null,
                    $santri->wali ? $santri->wali->nama_wali : null,
                    $santri->wali ? $santri->wali->no_hp : null,
                    $santri->wali ? $santri->wali->email : null
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function template()
    {
        $filename = "santri_template.csv";
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        return response()->stream(function() {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['nis', 'nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'nik_wali', 'nama_wali', 'no_hp_wali', 'email_wali']);
            fclose($handle);
        }, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), "r");
        
        $header = true;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($header) {
                $header = false;
                continue;
            }

            if (empty($data[0]) || empty($data[1])) continue;

            $wali = null;
            if (!empty($data[7]) || !empty($data[8])) {
                $wali = \App\Models\Wali::firstOrCreate(
                    ['nik' => $data[7] ?: null],
                    [
                        'nama_wali' => $data[8] ?: 'Tanpa Nama',
                        'no_hp' => $data[9] ?? null,
                        'email' => $data[10] ?? null,
                        'alamat' => $data[6] ?? null,
                    ]
                );
            }

            \App\Models\Santri::updateOrCreate(
                ['nis' => $data[0]],
                [
                    'nama' => $data[1],
                    'nik' => $data[2] ?? null,
                    'jenis_kelamin' => $data[3] ?? null,
                    'tempat_lahir' => $data[4] ?? null,
                    'tanggal_lahir' => $data[5] ?? null,
                    'alamat' => $data[6] ?? null,
                    'wali_id' => $wali ? $wali->id : null,
                ]
            );
        }
        fclose($handle);

        return response()->json(['message' => 'Import successful']);
    }

    public function templateExcel()
    {
        $template = collect([
            ['nis' => '', 'nama' => '', 'nik' => '', 'jenis_kelamin' => '', 'tempat_lahir' => '', 'tanggal_lahir' => '', 'alamat' => '', 'nik_wali' => '', 'nama_wali' => '', 'no_hp_wali' => '', 'email_wali' => '']
        ]);
        return (new \Rap2hpoutre\FastExcel\FastExcel($template))->download('template_santri.xlsx');
    }

    public function exportExcel()
    {
        $santris = \App\Models\Santri::with('wali')->get();
        return (new \Rap2hpoutre\FastExcel\FastExcel($santris))->download('export_santri.xlsx', function ($santri) {
            return [
                'nis' => $santri->nis,
                'nama' => $santri->nama,
                'nik' => $santri->nik,
                'jenis_kelamin' => $santri->jenis_kelamin,
                'tempat_lahir' => $santri->tempat_lahir,
                'tanggal_lahir' => $santri->tanggal_lahir,
                'alamat' => $santri->alamat,
                'nik_wali' => $santri->wali ? $santri->wali->nik : null,
                'nama_wali' => $santri->wali ? $santri->wali->nama_wali : null,
                'no_hp_wali' => $santri->wali ? $santri->wali->no_hp : null,
                'email_wali' => $santri->wali ? $santri->wali->email : null,
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
            if (empty($row['nis']) || empty($row['nama'])) continue;

            $wali = null;
            if (!empty($row['nik_wali']) || !empty($row['nama_wali'])) {
                $wali = \App\Models\Wali::firstOrCreate(
                    ['nik' => $row['nik_wali'] ?: null],
                    [
                        'nama_wali' => $row['nama_wali'] ?: 'Tanpa Nama',
                        'no_hp' => $row['no_hp_wali'] ?? null,
                        'email' => $row['email_wali'] ?? null,
                        'alamat' => $row['alamat'] ?? null,
                    ]
                );
            }

            \App\Models\Santri::updateOrCreate(
                ['nis' => $row['nis']],
                [
                    'nama' => $row['nama'],
                    'nik' => $row['nik'] ?? null,
                    'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                    'alamat' => $row['alamat'] ?? null,
                    'wali_id' => $wali ? $wali->id : null,
                ]
            );
            $importedCount++;
        }

        return response()->json(['message' => "Berhasil mengimpor $importedCount data Santri dari Excel."]);
    }
}
