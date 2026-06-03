<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    $logPath = storage_path('logs/laravel.log');
    if (!file_exists($logPath)) {
        echo json_encode(['error' => 'No log file']);
        exit;
    }
    $lines = file($logPath);
    echo json_encode(array_slice($lines, -150));
} catch (\Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
