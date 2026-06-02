<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BoyongController;
use App\Http\Controllers\SuratPermohonanController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    Route::get('/santri/export/csv', [\App\Http\Controllers\SantriController::class, 'export']);
    Route::get('/santri/template/csv', [\App\Http\Controllers\SantriController::class, 'template']);
    Route::post('/santri/import/csv', [\App\Http\Controllers\SantriController::class, 'import']);
    Route::get('/santri/export/excel', [\App\Http\Controllers\SantriController::class, 'exportExcel']);
    Route::get('/santri/template/excel', [\App\Http\Controllers\SantriController::class, 'templateExcel']);
    Route::post('/santri/import/excel', [\App\Http\Controllers\SantriController::class, 'importExcel']);

    Route::get('/badkom/export/csv', [\App\Http\Controllers\BadkomController::class, 'export']);
    Route::get('/badkom/template/csv', [\App\Http\Controllers\BadkomController::class, 'template']);
    Route::post('/badkom/import/csv', [\App\Http\Controllers\BadkomController::class, 'import']);
    Route::get('/badkom/export/excel', [\App\Http\Controllers\BadkomController::class, 'exportExcel']);
    Route::get('/badkom/template/excel', [\App\Http\Controllers\BadkomController::class, 'templateExcel']);
    Route::post('/badkom/import/excel', [\App\Http\Controllers\BadkomController::class, 'importExcel']);

    Route::get('/pjutd/export/csv', [\App\Http\Controllers\PjutdController::class, 'export']);
    Route::get('/pjutd/template/csv', [\App\Http\Controllers\PjutdController::class, 'template']);
    Route::post('/pjutd/import/csv', [\App\Http\Controllers\PjutdController::class, 'import']);
    Route::get('/pjutd/export/excel', [\App\Http\Controllers\PjutdController::class, 'exportExcel']);
    Route::get('/pjutd/template/excel', [\App\Http\Controllers\PjutdController::class, 'templateExcel']);
    Route::post('/pjutd/import/excel', [\App\Http\Controllers\PjutdController::class, 'importExcel']);
    Route::get('/wali/by-nik/{nik}', [\App\Http\Controllers\WaliController::class, 'byNik']);
    Route::apiResource('santri', \App\Http\Controllers\SantriController::class);
    Route::apiResource('badkom', \App\Http\Controllers\BadkomController::class);
    Route::apiResource('pjutd', \App\Http\Controllers\PjutdController::class);
    Route::apiResource('utd', \App\Http\Controllers\UtdController::class);
    Route::get('/tahun-ajaran/active', [\App\Http\Controllers\TahunAjaranController::class, 'active']);
    Route::apiResource('tahun-ajaran', \App\Http\Controllers\TahunAjaranController::class);
    
    Route::get('/penilaian', [\App\Http\Controllers\PenilaianController::class, 'index']);
    Route::post('/penilaian', [\App\Http\Controllers\PenilaianController::class, 'store']);
    Route::put('/penilaian/{id}/status', [\App\Http\Controllers\PenilaianController::class, 'updateStatus']);
    
    Route::get('/mutasi', [MutasiController::class, 'index']);
    Route::post('/mutasi', [MutasiController::class, 'store']);

    Route::get('/penarikan', [\App\Http\Controllers\PenarikanController::class, 'index']);
    Route::post('/penarikan', [\App\Http\Controllers\PenarikanController::class, 'store']);
    Route::get('/penilaian-pjutd', [\App\Http\Controllers\PenilaianPjutdController::class, 'index']);
    Route::post('/penilaian-pjutd', [\App\Http\Controllers\PenilaianPjutdController::class, 'store']);
    
    Route::get('/cetak/surat-lulus-tugas/{id}', [\App\Http\Controllers\SuratKelulusanController::class, 'cetak']);
    Route::get('/cetak/laporan-insidental/{id}', [\App\Http\Controllers\LaporanMendesakController::class, 'cetak']);
    Route::get('/cetak/penugasan', [\App\Http\Controllers\UtdController::class, 'cetak']);
    Route::get('/cetak/surat-permohonan/{id}', [SuratPermohonanController::class, 'cetak']);
    
    Route::apiResource('surat-permohonan', SuratPermohonanController::class);
    
    Route::apiResource('users', \App\Http\Controllers\UserController::class);
    Route::post('/users/{id}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword']);

    Route::get('/wilayah/provinsi', [\App\Http\Controllers\WilayahController::class, 'provinsi']);
    Route::get('/wilayah/kabupaten/{id}', [\App\Http\Controllers\WilayahController::class, 'kabupaten']);
    Route::get('/wilayah/kecamatan/{id}', [\App\Http\Controllers\WilayahController::class, 'kecamatan']);
    Route::get('/wilayah/kelurahan/{id}', [\App\Http\Controllers\WilayahController::class, 'kelurahan']);
    Route::get('/wilayah/parse-nik/{nik}', [\App\Http\Controllers\WilayahController::class, 'parseNik']);
    // Laporan Wajib & Soal
    Route::apiResource('soal-laporan', \App\Http\Controllers\SoalLaporanController::class);
    Route::get('/laporan-wajib/soal', [\App\Http\Controllers\LaporanWajibController::class, 'getSoal']);
    Route::post('/laporan-wajib/submit', [\App\Http\Controllers\LaporanWajibController::class, 'submit']);
    Route::get('/laporan-wajib', [\App\Http\Controllers\LaporanWajibController::class, 'index']);

    // Laporan Mendesak
    Route::get('/laporan-mendesak', [\App\Http\Controllers\LaporanMendesakController::class, 'index']);
    Route::post('/laporan-mendesak', [\App\Http\Controllers\LaporanMendesakController::class, 'store']);
    Route::put('/laporan-mendesak/{id}/status', [\App\Http\Controllers\LaporanMendesakController::class, 'updateStatus']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index']);
    Route::post('/settings/bulk', [SettingController::class, 'updateBulk']);
    Route::post('/settings/kop', [SettingController::class, 'uploadKop']);

    // Boyong
    Route::get('/boyong', [BoyongController::class, 'index']);
    Route::post('/boyong', [BoyongController::class, 'store']);
    Route::post('/boyong/manual', [BoyongController::class, 'storeManual']);
    Route::put('/boyong/{id}/status', [BoyongController::class, 'updateStatus']);

    // Mutasi
    Route::get('/mutasi', [MutasiController::class, 'index']);
    Route::post('/mutasi', [MutasiController::class, 'store']);

    // Profil
    Route::get('/profil', [ProfileController::class, 'show']);
    Route::post('/profil', [ProfileController::class, 'update']); // Use POST because of multipart/form-data
});
