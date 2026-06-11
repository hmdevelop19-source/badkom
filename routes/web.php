<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/setup-prod', function (\Illuminate\Http\Request $request) {
    // Pengamanan sederhana dengan password GET parameter
    if ($request->query('key') !== 'badkom123') {
        return response('Unauthorized', 401);
    }

    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $storageOutput = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        return response()->json([
            'message' => 'Setup berhasil dijalankan!',
            'migrate' => $migrateOutput,
            'storage' => $storageOutput
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
