<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = \App\Models\Santri::with(['utds.penilaian', 'utds.mutasis.asalPjutd', 'utds.mutasis.tujuanPjutd', 'boyong', 'wali'])->orderBy('id', 'desc');

        if ($request->has('status')) {
            $query->where('status_santri', $request->query('status'));
        } else {
            $query->where('status_santri', '!=', 'Alumni'); // Default hide alumni
        }

        if ($user) {
            if ($user->level === 'badkom_wilayah') {
                $query->whereHas('utds.pjutd', function($q) use ($user) {
                    $q->where('badkom_id', $user->badkom_id);
                });
            } elseif ($user->level === 'pjutd') {
                $query->whereHas('utds', function($q) use ($user) {
                    $q->where('pjutd_id', $user->pjutd_id);
                });
            } elseif ($user->level === 'utd') {
                $query->where('id', $user->santri_id);
            }
        }

        return response()->json($query->get());
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
            'keahlian' => 'nullable|string',
            
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

        // Auto-generate user account for Santri
        \App\Models\User::firstOrCreate(
            ['username' => $santri->nis],
            [
                'fullname' => $santri->nama,
                'email' => $santri->nis . '@ebadkom.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'level' => 'utd',
                'santri_id' => $santri->id,
            ]
        );

        return response()->json($santri, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $santri = \App\Models\Santri::with(['utds.pjutd', 'utds.tahunAjaran', 'utds.penilaian', 'utds.mutasis.asalPjutd', 'utds.mutasis.tujuanPjutd', 'wali', 'boyong'])->findOrFail($id);
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
            'keahlian' => 'nullable|string',

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
            fputcsv($handle, ['nis', 'nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'keahlian', 'nik_wali', 'nama_wali', 'no_hp_wali', 'email_wali'], ';');
            foreach ($santris as $santri) {
                fputcsv($handle, [
                    $santri->nis, $santri->nama, $santri->nik, $santri->jenis_kelamin, 
                    $santri->tempat_lahir, $santri->tanggal_lahir, $santri->alamat, $santri->keahlian, 
                    $santri->wali ? $santri->wali->nik : null,
                    $santri->wali ? $santri->wali->nama_wali : null,
                    $santri->wali ? $santri->wali->no_hp : null,
                    $santri->wali ? $santri->wali->email : null
                ], ';');
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
            fputcsv($handle, ['nis', 'nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'keahlian', 'nik_wali', 'nama_wali', 'no_hp_wali', 'email_wali'], ';');
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
        
        $defaultPassword = \Illuminate\Support\Facades\Hash::make('password');
        $header = true;
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            if ($header) {
                $header = false;
                continue;
            }

            if (empty($data[0]) || empty($data[1])) continue;

            $wali = null;
            if (!empty($data[8]) || !empty($data[9])) {
                $wali = \App\Models\Wali::firstOrCreate(
                    ['nik' => $data[8] ?: null],
                    [
                        'nama_wali' => $data[9] ?: 'Tanpa Nama',
                        'no_hp' => $data[10] ?? null,
                        'email' => $data[11] ?? null,
                        'alamat' => $data[6] ?? null,
                    ]
                );
            }

            $santri = \App\Models\Santri::updateOrCreate(
                ['nis' => $data[0]],
                [
                    'nama' => $data[1],
                    'nik' => $data[2] ?? null,
                    'jenis_kelamin' => $data[3] ?? null,
                    'tempat_lahir' => $data[4] ?? null,
                    'tanggal_lahir' => $data[5] ?? null,
                    'alamat' => $data[6] ?? null,
                    'keahlian' => $data[7] ?? null,
                    'wali_id' => $wali ? $wali->id : null,
                ]
            );

            \App\Models\User::firstOrCreate(
                ['username' => $santri->nis],
                [
                    'fullname' => $santri->nama,
                    'email' => $santri->nis . '@ebadkom.com',
                    'password' => $defaultPassword,
                    'level' => 'utd',
                    'santri_id' => $santri->id,
                ]
            );
        }
        fclose($handle);

        return response()->json(['message' => 'Import successful']);
    }

    public function templateExcel()
    {
        $template = collect([
            ['nis' => '', 'nama' => '', 'nik' => '', 'jenis_kelamin' => '', 'tempat_lahir' => '', 'tanggal_lahir' => '', 'alamat' => '', 'keahlian' => '', 'nik_wali' => '', 'nama_wali' => '', 'no_hp_wali' => '', 'email_wali' => '']
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
                'keahlian' => $santri->keahlian,
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
        $defaultPassword = \Illuminate\Support\Facades\Hash::make('password');

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

            $santri = \App\Models\Santri::updateOrCreate(
                ['nis' => $row['nis']],
                [
                    'nama' => $row['nama'],
                    'nik' => $row['nik'] ?? null,
                    'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                    'alamat' => $row['alamat'] ?? null,
                    'keahlian' => $row['keahlian'] ?? null,
                    'wali_id' => $wali ? $wali->id : null,
                ]
            );

            \App\Models\User::firstOrCreate(
                ['username' => $santri->nis],
                [
                    'fullname' => $santri->nama,
                    'email' => $santri->nis . '@ebadkom.com',
                    'password' => $defaultPassword,
                    'level' => 'utd',
                    'santri_id' => $santri->id,
                ]
            );
            $importedCount++;
        }

        return response()->json(['message' => "Berhasil mengimpor $importedCount data Santri dari Excel."]);
    }
}
