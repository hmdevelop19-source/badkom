<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Mulai menyisipkan data Wilayah dari CSV...');
        $basePath = storage_path('app/wilayah/node_modules/idn-area-data/data');

        if (!file_exists($basePath . '/provinces.csv')) {
            $this->command->error('File CSV tidak ditemukan! Pastikan sudah menginstall idn-area-data di storage/app/wilayah');
            return;
        }

        DB::statement('PRAGMA foreign_keys = OFF;');
        Kelurahan::truncate();
        Kecamatan::truncate();
        Kabupaten::truncate();
        Provinsi::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        // 1. PROVINSI
        $this->command->info('Memproses Provinsi...');
        $provData = [];
        $file = fopen($basePath . '/provinces.csv', 'r');
        fgetcsv($file); // skip header
        while (($row = fgetcsv($file)) !== false) {
            $provData[] = [
                'kode' => str_replace('.', '', $row[0]),
                'nama' => $row[1],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        fclose($file);
        Provinsi::insert($provData);
        $provMap = Provinsi::pluck('id', 'kode')->toArray();

        // 2. KABUPATEN
        $this->command->info('Memproses Kabupaten...');
        $kabData = [];
        $file = fopen($basePath . '/regencies.csv', 'r');
        fgetcsv($file);
        while (($row = fgetcsv($file)) !== false) {
            $parentCode = str_replace('.', '', $row[1]);
            if (isset($provMap[$parentCode])) {
                $kabData[] = [
                    'provinsi_id' => $provMap[$parentCode],
                    'kode' => str_replace('.', '', $row[0]),
                    'nama' => $row[2],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        fclose($file);
        foreach (array_chunk($kabData, 100) as $chunk) Kabupaten::insert($chunk);
        $kabMap = Kabupaten::pluck('id', 'kode')->toArray();

        // 3. KECAMATAN
        $this->command->info('Memproses Kecamatan...');
        $kecData = [];
        $file = fopen($basePath . '/districts.csv', 'r');
        fgetcsv($file);
        while (($row = fgetcsv($file)) !== false) {
            $parentCode = str_replace('.', '', $row[1]);
            if (isset($kabMap[$parentCode])) {
                $kecData[] = [
                    'kabupaten_id' => $kabMap[$parentCode],
                    'kode' => str_replace('.', '', $row[0]),
                    'nama' => $row[2],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        fclose($file);
        foreach (array_chunk($kecData, 100) as $chunk) Kecamatan::insert($chunk);
        $kecMap = Kecamatan::pluck('id', 'kode')->toArray();

        DB::disableQueryLog();
        // 4. KELURAHAN
        $this->command->info('Memproses Kelurahan (~83.000 data, harap tunggu)...');
        $kelData = [];
        $file = fopen($basePath . '/villages.csv', 'r');
        fgetcsv($file);
        while (($row = fgetcsv($file)) !== false) {
            $parentCode = str_replace('.', '', $row[1]);
            if (isset($kecMap[$parentCode])) {
                $kelData[] = [
                    'kecamatan_id' => $kecMap[$parentCode],
                    'kode' => str_replace('.', '', $row[0]),
                    'nama' => $row[2],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Insert chunk of 100 to save memory and avoid SQLite limits
            if (count($kelData) >= 100) {
                Kelurahan::insert($kelData);
                $kelData = [];
            }
        }
        
        // Insert remainder
        if (count($kelData) > 0) {
            Kelurahan::insert($kelData);
        }
        fclose($file);
        
        $this->command->info('Selesai! Seluruh data wilayah telah disisipkan.');
    }
}
