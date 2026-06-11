<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreSantriRequest;
use App\Http\Requests\UpdateSantriRequest;
use App\Services\SantriService;
use Rap2hpoutre\FastExcel\FastExcel;

class SantriController extends Controller
{
    protected $santriService;

    public function __construct(SantriService $santriService)
    {
        $this->santriService = $santriService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $statusQuery = $request->query('status');
        
        $santris = $this->santriService->getFilteredSantris($user, $statusQuery);

        return response()->json($santris);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSantriRequest $request)
    {
        $this->authorize('create', \App\Models\Santri::class);

        try {
            $santri = $this->santriService->createSantri($request->validated());
            return response()->json($santri, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan data', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $santri = $this->santriService->getSantriById($id);
        return response()->json($santri);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSantriRequest $request, string $id)
    {
        $santri = \App\Models\Santri::findOrFail($id);
        $this->authorize('update', $santri);

        try {
            $santri = $this->santriService->updateSantri($id, $request->validated());
            return response()->json($santri);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui data', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $santri = \App\Models\Santri::findOrFail($id);
        $this->authorize('delete', $santri);

        $this->santriService->deleteSantri($id);
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function export()
    {
        $santris = $this->santriService->getAllSantriCursor();
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

        try {
            $this->santriService->importCsv($request->file('file')->getPathname());
            return response()->json(['message' => 'Import successful']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengimpor data', 'message' => $e->getMessage()], 500);
        }
    }

    public function templateExcel()
    {
        $template = collect([
            ['nis' => '', 'nama' => '', 'nik' => '', 'jenis_kelamin' => '', 'tempat_lahir' => '', 'tanggal_lahir' => '', 'alamat' => '', 'keahlian' => '', 'nik_wali' => '', 'nama_wali' => '', 'no_hp_wali' => '', 'email_wali' => '']
        ]);
        return (new FastExcel($template))->download('template_santri.xlsx');
    }

    public function exportExcel()
    {
        $santris = $this->santriService->getAllSantriCursor();
        return (new FastExcel($santris))->download('export_santri.xlsx', function ($santri) {
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

        try {
            $importedCount = $this->santriService->importExcel($request->file('file'));
            return response()->json(['message' => "Berhasil mengimpor $importedCount data Santri dari Excel."]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengimpor data', 'message' => $e->getMessage()], 500);
        }
    }
}
