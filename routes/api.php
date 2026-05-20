<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('santri', \App\Http\Controllers\SantriController::class);
    Route::apiResource('badkom', \App\Http\Controllers\BadkomController::class);
    Route::apiResource('pjutd', \App\Http\Controllers\PjutdController::class);

    Route::get('/wilayah/provinsi', [\App\Http\Controllers\WilayahController::class, 'provinsi']);
    Route::get('/wilayah/kabupaten/{id}', [\App\Http\Controllers\WilayahController::class, 'kabupaten']);
    Route::get('/wilayah/kecamatan/{id}', [\App\Http\Controllers\WilayahController::class, 'kecamatan']);
    Route::get('/wilayah/kelurahan/{id}', [\App\Http\Controllers\WilayahController::class, 'kelurahan']);
    Route::get('/wilayah/parse-nik/{nik}', [\App\Http\Controllers\WilayahController::class, 'parseNik']);
});
