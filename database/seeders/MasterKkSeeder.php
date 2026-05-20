<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Agama;
use App\Models\Pendidikan;
use App\Models\Pekerjaan;
use App\Models\GolonganDarah;
use App\Models\StatusPerkawinan;
use App\Models\StatusKeluarga;

class MasterKkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agamas = ['ISLAM', 'KRISTEN', 'KATHOLIK', 'HINDU', 'BUDHA', 'KONGHUCU', 'ALIRAN KEPERCAYAAN'];
        foreach ($agamas as $nama) Agama::create(['nama' => $nama]);

        $pendidikans = ['TIDAK / BELUM SEKOLAH', 'BELUM TAMAT SD/SEDERAJAT', 'TAMAT SD / SEDERAJAT', 'SLTP/SEDERAJAT', 'SLTA / SEDERAJAT', 'DIPLOMA I / II', 'AKADEMI/ DIPLOMA III/S. MUDA', 'DIPLOMA IV/ STRATA I', 'STRATA II', 'STRATA III'];
        foreach ($pendidikans as $nama) Pendidikan::create(['nama' => $nama]);

        $pekerjaans = ['BELUM/TIDAK BEKERJA', 'MENGURUS RUMAH TANGGA', 'PELAJAR/MAHASISWA', 'PENSIUNAN', 'PEGAWAI NEGERI SIPIL', 'TENTARA NASIONAL INDONESIA', 'KEPOLISIAN RI', 'PERDAGANGAN', 'PETANI/PEKEBUN', 'PETERNAK', 'NELAYAN/PERIKANAN', 'INDUSTRI', 'KONSTRUKSI', 'TRANSPORTASI', 'KARYAWAN SWASTA', 'KARYAWAN BUMN', 'KARYAWAN BUMD', 'KARYAWAN HONORER', 'BURUH HARIAN LEPAS', 'BURUH TANI/PERKEBUNAN', 'BURUH NELAYAN/PERIKANAN', 'BURUH PETERNAKAN', 'PEMBANTU RUMAH TANGGA', 'TUKANG CUKUR', 'TUKANG LISTRIK', 'TUKANG BATU', 'TUKANG KAYU', 'TUKANG SOL SEPATU', 'TUKANG LAS/PANDAI BESI', 'TUKANG JAHIT', 'TUKANG GIGI', 'PENATA RIAS', 'PENATA BUSANA', 'PENATA RAMBUT', 'MEKANIK', 'SENIMAN', 'TABIB', 'PARAJI', 'PERANCANG BUSANA', 'PENTERJEMAH', 'IMAM MASJID', 'PENDETA', 'PASTOR', 'WARTAWAN', 'USTADZ/MUBALIGH', 'JURU MASAK', 'PROMOTOR ACARA', 'ANGGOTA DPR-RI', 'ANGGOTA DPD', 'ANGGOTA BPK', 'PRESIDEN', 'WAKIL PRESIDEN', 'ANGGOTA MAHKAMAH KONSTITUSI', 'ANGGOTA KABINET/KEMENTERIAN', 'DUTA BESAR', 'GUBERNUR', 'WAKIL GUBERNUR', 'BUPATI', 'WAKIL BUPATI', 'WALIKOTA', 'WAKIL WALIKOTA', 'ANGGOTA DPRD PROVINSI', 'ANGGOTA DPRD KABUPATEN/KOTA', 'DOSEN', 'GURU', 'PILOT', 'PENGACARA', 'NOTARIS', 'ARSITEK', 'AKUNTAN', 'KONSULTAN', 'DOKTER', 'BIDAN', 'PERAWAT', 'APOTEKER', 'PSIKIATER/PSIKOLOG', 'PENYIAR TELEVISI', 'PENYIAR RADIO', 'PELAUT', 'PENELITI', 'SOPIR', 'PIALANG', 'PARANORMAL', 'PEDAGANG', 'PERANGKAT DESA', 'KEPALA DESA', 'BIARAWATI', 'WIRASWASTA'];
        foreach ($pekerjaans as $nama) Pekerjaan::create(['nama' => $nama]);

        $golongan_darahs = ['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'TIDAK TAHU'];
        foreach ($golongan_darahs as $nama) GolonganDarah::create(['nama' => $nama]);

        $status_perkawinans = ['BELUM KAWIN', 'KAWIN', 'CERAI HIDUP', 'CERAI MATI'];
        foreach ($status_perkawinans as $nama) StatusPerkawinan::create(['nama' => $nama]);

        $status_keluargas = ['KEPALA KELUARGA', 'SUAMI', 'ISTRI', 'ANAK', 'MENANTU', 'CUCU', 'ORANGTUA', 'MERTUA', 'FAMILI LAIN', 'PEMBANTU', 'LAINNYA'];
        foreach ($status_keluargas as $nama) StatusKeluarga::create(['nama' => $nama]);
    }
}
