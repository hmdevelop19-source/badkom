<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Santri::create([
            'nis' => '12345',
            'nama' => 'Santri One',
            'id_prov' => 1,
            'id_kab' => 1,
            'id_kec' => 1,
            'id_kel' => 1,
        ]);
        
        \App\Models\Santri::create([
            'nis' => '67890',
            'nama' => 'Santri Two',
            'id_prov' => 1,
            'id_kab' => 1,
            'id_kec' => 1,
            'id_kel' => 1,
        ]);
    }
}
