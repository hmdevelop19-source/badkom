<?php

namespace App\Services;

use App\Models\Santri;
use App\Models\Wali;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Rap2hpoutre\FastExcel\FastExcel;
use Exception;

class SantriService
{
    /**
     * Get filtered santri list based on user role and status
     */
    public function getFilteredSantris($user, $statusQuery = null)
    {
        $query = Santri::with(['utds.penilaian', 'utds.mutasis.asalPjutd', 'utds.mutasis.tujuanPjutd', 'boyong', 'wali'])
            ->orderBy('id', 'desc');

        if ($statusQuery) {
            $query->where('status_santri', $statusQuery);
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

        return $query->get();
    }

    /**
     * Get single santri with relations
     */
    public function getSantriById(string $id)
    {
        return Santri::with(['utds.pjutd', 'utds.tahunAjaran', 'utds.penilaian', 'utds.mutasis.asalPjutd', 'utds.mutasis.tujuanPjutd', 'wali', 'boyong'])
            ->findOrFail($id);
    }

    /**
     * Create new santri, wali (if exists), and user account
     */
    public function createSantri(array $validatedData)
    {
        DB::beginTransaction();
        try {
            $wali = null;
            if (!empty($validatedData['nik_wali']) || !empty($validatedData['nama_wali'])) {
                $wali = Wali::firstOrCreate(
                    ['nik' => $validatedData['nik_wali'] ?? null],
                    [
                        'nama_wali' => $validatedData['nama_wali'] ?? 'Tanpa Nama',
                        'no_hp' => $validatedData['no_hp_wali'] ?? null,
                        'email' => $validatedData['email_wali'] ?? null,
                        'id_prov' => $validatedData['id_prov'] ?? null,
                        'id_kab' => $validatedData['id_kab'] ?? null,
                        'id_kec' => $validatedData['id_kec'] ?? null,
                        'id_kel' => $validatedData['id_kel'] ?? null,
                        'alamat' => $validatedData['alamat'] ?? null,
                    ]
                );
            }

            $santriData = collect($validatedData)->except(['nik_wali', 'nama_wali', 'no_hp_wali', 'email_wali'])->toArray();
            if ($wali) {
                $santriData['wali_id'] = $wali->id;
            }

            $santri = Santri::create($santriData);

            // Auto-generate user account for Santri
            User::firstOrCreate(
                ['username' => $santri->nis],
                [
                    'fullname' => $santri->nama,
                    'email' => $santri->nis . '@ebadkom.com',
                    'password' => Hash::make('password'),
                    'level' => 'utd',
                    'santri_id' => $santri->id,
                ]
            );

            DB::commit();
            return $santri;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update existing santri and wali
     */
    public function updateSantri(string $id, array $validatedData)
    {
        $santri = Santri::findOrFail($id);

        DB::beginTransaction();
        try {
            $wali = null;
            if (!empty($validatedData['nik_wali']) || !empty($validatedData['nama_wali'])) {
                $wali = Wali::updateOrCreate(
                    ['nik' => $validatedData['nik_wali'] ?? null],
                    [
                        'nama_wali' => $validatedData['nama_wali'] ?? 'Tanpa Nama',
                        'no_hp' => $validatedData['no_hp_wali'] ?? null,
                        'email' => $validatedData['email_wali'] ?? null,
                        'id_prov' => $validatedData['id_prov'] ?? null,
                        'id_kab' => $validatedData['id_kab'] ?? null,
                        'id_kec' => $validatedData['id_kec'] ?? null,
                        'id_kel' => $validatedData['id_kel'] ?? null,
                        'alamat' => $validatedData['alamat'] ?? null,
                    ]
                );
            }

            $santriData = collect($validatedData)->except(['nik_wali', 'nama_wali', 'no_hp_wali', 'email_wali'])->toArray();
            if ($wali) {
                $santriData['wali_id'] = $wali->id;
            }

            $santri->update($santriData);

            DB::commit();
            return $santri;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete santri
     */
    public function deleteSantri(string $id)
    {
        $santri = Santri::findOrFail($id);
        $santri->delete();
        return true;
    }

    /**
     * Get all santri cursor for export
     */
    public function getAllSantriCursor()
    {
        foreach (Santri::with('wali')->lazyById(500) as $santri) {
            yield $santri;
        }
    }

    /**
     * Import santri from CSV file stream
     */
    public function importCsv($filePath)
    {
        $handle = fopen($filePath, "r");
        
        DB::beginTransaction();
        try {
            $defaultPassword = Hash::make('password');
            $header = true;
            
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if ($header) {
                    $header = false;
                    continue;
                }

                if (empty($data[0]) || empty($data[1])) continue;

                $wali = null;
                if (!empty($data[8]) || !empty($data[9])) {
                    $wali = Wali::firstOrCreate(
                        ['nik' => $data[8] ?: null],
                        [
                            'nama_wali' => $data[9] ?: 'Tanpa Nama',
                            'no_hp' => $data[10] ?? null,
                            'email' => $data[11] ?? null,
                            'alamat' => $data[6] ?? null,
                        ]
                    );
                }

                $santri = Santri::updateOrCreate(
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

                User::firstOrCreate(
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

            DB::commit();
            return true;
        } catch (Exception $e) {
            fclose($handle);
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import santri from Excel using FastExcel
     */
    public function importExcel($file)
    {
        $collection = (new FastExcel)->import($file);
        $importedCount = 0;
        $defaultPassword = Hash::make('password');

        foreach ($collection as $row) {
            $rawNis = $row['nis'] ?? null;
            $rawNama = $row['nama'] ?? null;
            
            if (empty($rawNis) || empty($rawNama)) continue;

            // 1. Perbaiki NIS & NIK jika terbaca sebagai Float (e.g. 1.44E+11)
            $nis = is_float($rawNis) ? number_format($rawNis, 0, '', '') : trim((string)$rawNis);
            $nis = preg_replace('/[^0-9]/', '', $nis); // Ambil angkanya saja jika tiba-tiba negatif
            if (empty($nis)) continue; // Skip jika nis invalid

            $rawNik = $row['nik'] ?? null;
            $nik = is_float($rawNik) ? number_format($rawNik, 0, '', '') : trim((string)$rawNik);
            if (empty($nik)) $nik = null;

            // 2. Perbaiki Jenis Kelamin (Tolak string kosong, paksa jadi null)
            $jk = isset($row['jenis_kelamin']) ? trim(strtoupper((string)$row['jenis_kelamin'])) : '';
            if ($jk !== 'L' && $jk !== 'P') {
                $jk = null;
            }

            // 3. Perbaiki Tanggal Lahir (Format Indonesia ke YYYY-MM-DD, handle DateTime object, & ubah '' jadi null)
            $tanggalLahir = null;
            if (isset($row['tanggal_lahir'])) {
                if ($row['tanggal_lahir'] instanceof \DateTimeInterface) {
                    $tanggalLahir = $row['tanggal_lahir']->format('Y-m-d');
                } else {
                    $tglRaw = trim((string)$row['tanggal_lahir']);
                    if ($tglRaw !== '') {
                        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglRaw)) {
                            $bulanIndo = [
                                'JANUARI' => '01', 'FEBRUARI' => '02', 'MARET' => '03', 'APRIL' => '04',
                                'MEI' => '05', 'JUNI' => '06', 'JULI' => '07', 'AGUSTUS' => '08',
                                'SEPTEMBER' => '09', 'OKTOBER' => '10', 'NOVEMBER' => '11', 'DESEMBER' => '12',
                                'JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04',
                                'AGU' => '08', 'SEP' => '09', 'OKT' => '10', 'NOV' => '11', 'DES' => '12'
                            ];
                            $tglStr = strtoupper($tglRaw);
                            foreach ($bulanIndo as $indo => $angka) {
                                $tglStr = str_replace($indo, $angka, $tglStr);
                            }
                            try {
                                $tanggalLahir = \Carbon\Carbon::parse($tglStr)->format('Y-m-d');
                            } catch (\Exception $e) {
                                $tanggalLahir = null;
                            }
                        } else {
                            $tanggalLahir = $tglRaw;
                        }
                    }
                }
            }

            $wali = null;
            if (!empty($row['nik_wali']) || !empty($row['nama_wali'])) {
                $wali = Wali::firstOrCreate(
                    ['nik' => $row['nik_wali'] ?: null],
                    [
                        'nama_wali' => $row['nama_wali'] ?: 'Tanpa Nama',
                        'no_hp' => $row['no_hp_wali'] ?? null,
                        'email' => $row['email_wali'] ?? null,
                        'alamat' => $row['alamat'] ?? null,
                    ]
                );
            }

            $santri = Santri::updateOrCreate(
                ['nis' => $nis],
                [
                    'nama' => $rawNama,
                    'nik' => $nik,
                    'jenis_kelamin' => $jk,
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $tanggalLahir,
                    'alamat' => $row['alamat'] ?? null,
                    'keahlian' => $row['keahlian'] ?? null,
                    'wali_id' => $wali ? $wali->id : null,
                ]
            );

            User::firstOrCreate(
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

        return $importedCount;
    }
}
