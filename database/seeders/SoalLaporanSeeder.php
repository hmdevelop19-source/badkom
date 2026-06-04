<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SoalLaporan;

class SoalLaporanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus data lama (SQLite compatible)
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        
        SoalLaporan::query()->delete();
        
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // ==========================================
        // SOAL UNTUK UT-D (target_level: utd)
        // Mulai ID 1 - 40
        // ==========================================
        $utdSoals = [
            // C. KEGIATAN MADRASAH
            ['id' => 1, 'pertanyaan' => 'Dimanfaatkan sebagai guru wali kelas (Pilih tingkat)', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['MI', 'MTs', 'MA', 'Tidak']],
            ['id' => 2, 'pertanyaan' => 'Dimanfaatkan sebagai guru wali kelas (Pilih kelas)', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['1', '2', '3', '4', '5', '6', 'Tidak']],
            ['id' => 3, 'pertanyaan' => 'Dimanfaatkan sebagai guru fan kelas (Pilih tingkat)', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['MI', 'MTs', 'MA', 'Tidak']],
            ['id' => 4, 'pertanyaan' => 'Dimanfaatkan sebagai guru fan kelas (Pilih kelas)', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['1', '2', '3', '4', '5', '6', 'Tidak']],
            ['id' => 5, 'pertanyaan' => 'Kelas yang dimasuki berisi murid', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Banin', 'Banat', 'Campuran']],
            
            ['id' => 6, 'pertanyaan' => 'Bulan ini masuk kelas sebanyak (Hari)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 7, 'pertanyaan' => 'Bulan ini masuk kelas sebanyak (Jam Pelajaran)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            
            ['id' => 8, 'pertanyaan' => 'Tidak masuk kelas karena sakit (Hari)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 9, 'pertanyaan' => 'Tidak masuk kelas karena sakit (Jam Pelajaran)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            
            ['id' => 10, 'pertanyaan' => 'Tidak masuk kelas karena pulang (Hari)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 11, 'pertanyaan' => 'Tidak masuk kelas karena pulang (Jam Pelajaran)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            
            ['id' => 12, 'pertanyaan' => 'Tidak masuk kelas karena udzur lain (Hari)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 13, 'pertanyaan' => 'Tidak masuk kelas karena udzur lain (Jam Pelajaran)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            
            ['id' => 14, 'pertanyaan' => 'Jumlah tidak masuk selama satu bulan (Hari)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 15, 'pertanyaan' => 'Jumlah tidak masuk selama satu bulan (Jam Pelajaran)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            
            ['id' => 16, 'pertanyaan' => 'Jumlah jam wajib mengajar bulan ini (Hari)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 17, 'pertanyaan' => 'Jumlah jam wajib mengajar bulan ini (Jam Pelajaran)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            
            ['id' => 18, 'pertanyaan' => 'Jumlah jam wajib mengajar sepekan (Hari)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 19, 'pertanyaan' => 'Jumlah jam wajib mengajar sepekan (Jam Pelajaran)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            
            ['id' => 20, 'pertanyaan' => 'Menangani administrasi Absensi Murid', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Ikut', 'Tidak ikut']],
            ['id' => 21, 'pertanyaan' => 'Menangani administrasi Buku Raport', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Ikut', 'Tidak ikut']],
            ['id' => 22, 'pertanyaan' => 'Menangani administrasi Buku Tabungan', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Ikut', 'Tidak ikut']],
            ['id' => 23, 'pertanyaan' => 'Menangani administrasi lain-lain (sebutkan & ikut/tidak)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],

            // D. KEGIATAN EKSTRA
            ['id' => 24, 'pertanyaan' => 'Mengajar Al-Qur\'an bil-tartil', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Ya', 'Tidak']],
            ['id' => 25, 'pertanyaan' => 'Waktu mengajar Al-Qur\'an bil-tartil (Jam)', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Pagi', 'Siang', 'Malam', '-']],
            
            ['id' => 26, 'pertanyaan' => 'Mengajar kitab', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Ya', 'Tidak']],
            ['id' => 27, 'pertanyaan' => 'Waktu mengajar kitab (Jam)', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Pagi', 'Siang', 'Malam', '-']],
            
            ['id' => 28, 'pertanyaan' => 'Ditunjuk sebagai imam rowatib', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Ya', 'Tidak']],
            ['id' => 29, 'pertanyaan' => 'Tempat menjadi imam rowatib', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Masjid', 'Musholla', 'Surau', '-']],
            
            ['id' => 30, 'pertanyaan' => 'Kegiatan Ekstra lain (4)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 31, 'pertanyaan' => 'Kegiatan Ekstra lain (5)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],

            // E. KOMUNIKASI ANTAR SESAMA
            ['id' => 32, 'pertanyaan' => 'Komunikasi dengan PJUT-D', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Sering', 'Jarang', 'Tidak pernah']],
            ['id' => 33, 'pertanyaan' => 'Komunikasi dengan Kepala Madrasah', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Sering', 'Jarang', 'Tidak pernah']],
            ['id' => 34, 'pertanyaan' => 'Komunikasi dengan guru yang lain', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Sering', 'Jarang', 'Tidak pernah']],
            ['id' => 35, 'pertanyaan' => 'Komunikasi dengan masyarakat umum', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Sering', 'Jarang', 'Tidak pernah']],

            // F. BISYAROH
            ['id' => 36, 'pertanyaan' => 'Bisyaroh dari PJUT-D bulan ini (Rp)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 37, 'pertanyaan' => 'Tunjangan lain (Rp)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 38, 'pertanyaan' => 'Tunjangan tambahan lainnya (sebutkan asalnya & Rp)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],

            // G & H
            ['id' => 39, 'pertanyaan' => 'G. KENDALA-KENDALA (Deskripsikan per poin)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 40, 'pertanyaan' => 'H. LAIN-LAIN', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
        ];

        foreach ($utdSoals as $s) {
            SoalLaporan::create(array_merge($s, ['target_level' => 'utd', 'is_active' => true]));
        }

        // ==========================================
        // SOAL UNTUK PJUT-D (target_level: pjutd)
        // Mulai ID 41 - 70
        // ==========================================
        $pjutdSoals = [
            // C. KEGIATAN UT-D DI RUANG KELAS
            ['id' => 41, 'pertanyaan' => 'Dimanfaatkan menjadi guru wali kelas (Pilih tingkat)', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['MI', 'MTs', 'MA', 'Tidak']],
            ['id' => 42, 'pertanyaan' => 'Dimanfaatkan menjadi guru wali kelas (Pilih kelas)', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['1', '2', '3', '4', '5', '6', 'Tidak']],
            ['id' => 43, 'pertanyaan' => 'Dimanfaatkan menjadi guru fan kelas (Pilih tingkat)', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['MI', 'MTs', 'MA', 'Tidak']],
            ['id' => 44, 'pertanyaan' => 'Dimanfaatkan menjadi guru fan kelas (Pilih kelas)', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['1', '2', '3', '4', '5', '6', 'Tidak']],
            ['id' => 45, 'pertanyaan' => 'Dimanfaatkan mengajar murid', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Banin', 'Banat', 'Campuran']],
            ['id' => 46, 'pertanyaan' => 'Ustadz Tugas & Da\'i (UT-D) masuk kelas', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Rajin', 'Jarang', 'Tidak aktif']],

            // D. KEGIATAN LUAR KELAS
            ['id' => 47, 'pertanyaan' => 'Jenis Kegiatan luar kelas 1 (Isi Jenis Kegiatan)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 48, 'pertanyaan' => 'Waktu Kegiatan 1', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 49, 'pertanyaan' => 'Sifat Kegiatan 1', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Baru', 'Meneruskan', '-']],
            
            ['id' => 50, 'pertanyaan' => 'Jenis Kegiatan luar kelas 2 (Isi Jenis Kegiatan)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 51, 'pertanyaan' => 'Waktu Kegiatan 2', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 52, 'pertanyaan' => 'Sifat Kegiatan 2', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Baru', 'Meneruskan', '-']],
            
            ['id' => 53, 'pertanyaan' => 'Jenis Kegiatan luar kelas 3 (Isi Jenis Kegiatan)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 54, 'pertanyaan' => 'Waktu Kegiatan 3', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 55, 'pertanyaan' => 'Sifat Kegiatan 3', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Baru', 'Meneruskan', '-']],

            // E. KETERTIBAN
            ['id' => 56, 'pertanyaan' => 'Waktu menulis laporan ini, keadaan rambut UT-D', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Pendek', 'Melebihi batas']],
            ['id' => 57, 'pertanyaan' => 'Sampai laporan ini, UT-D pernah bepergian (Berapa Kali)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 58, 'pertanyaan' => 'Total bepergian (Berapa Hari)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 59, 'pertanyaan' => 'Sampai laporan ini, UT-D pernah pulang (Berapa Kali)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 60, 'pertanyaan' => 'Total pulang (Berapa Hari)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 61, 'pertanyaan' => 'Keperluan UT-D saat pulang', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 62, 'pertanyaan' => 'Pernah melakukan pelanggaran berupa', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 63, 'pertanyaan' => 'Langkah menanggulangi pelanggaran tersebut', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 64, 'pertanyaan' => 'Surat idzin YALMI yang dipakai (Lembar)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
            ['id' => 65, 'pertanyaan' => 'Sisa Surat idzin YALMI (Lembar)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],

            // F. HUBUNGAN
            ['id' => 66, 'pertanyaan' => 'Hubungan dengan guru-guru yang lain', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Baik', 'Kurang baik']],
            ['id' => 67, 'pertanyaan' => 'Hubungan dengan kami (Penanggung Jawab UT-D)', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Baik', 'Kurang baik']],
            ['id' => 68, 'pertanyaan' => 'Hubungan dengan Kepala Madrasah', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Baik', 'Kurang baik']],
            ['id' => 69, 'pertanyaan' => 'Hubungan murid dengan Ustadz tugas & Da\'i (UT-D)', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Baik', 'Kurang baik']],
            ['id' => 70, 'pertanyaan' => 'Hubungan dengan murid didalam kelas', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Akrab', 'Kurang']],
            ['id' => 71, 'pertanyaan' => 'Hubungan dengan murid diluar kelas', 'tipe_soal' => 'pilihan_ganda', 'opsi_jawaban' => ['Akrab', 'Kurang']],
            
            // F7 - Bisyaroh
            ['id' => 72, 'pertanyaan' => 'Bisyaroh 1 bulan kepada UT-D sebesar (Rp)', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],

            // G. LAIN-LAIN
            ['id' => 73, 'pertanyaan' => 'Lain-lain yang dipandang perlu untuk diketahui', 'tipe_soal' => 'uraian', 'opsi_jawaban' => null],
        ];

        foreach ($pjutdSoals as $s) {
            SoalLaporan::create(array_merge($s, ['target_level' => 'pjutd', 'is_active' => true]));
        }
    }
}
