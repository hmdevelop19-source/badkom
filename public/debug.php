<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    $schema = \Illuminate\Support\Facades\DB::select("PRAGMA table_info(pjutds)");
    echo json_encode($schema);
} catch (\Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
