<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('pjutd_id', '!=', null)->first();
if ($user) {
    $pjutd = \App\Models\Pjutd::find($user->pjutd_id);
    echo "User pjutd_id type: " . gettype($user->pjutd_id) . " value: " . $user->pjutd_id . "\n";
    echo "Pjutd id type: " . gettype($pjutd->id) . " value: " . $pjutd->id . "\n";
    echo "Strict equality (===): " . ($user->pjutd_id === $pjutd->id ? 'true' : 'false') . "\n";
    echo "Loose equality (==): " . ($user->pjutd_id == $pjutd->id ? 'true' : 'false') . "\n";
} else {
    echo "No PJUTD user found\n";
}
