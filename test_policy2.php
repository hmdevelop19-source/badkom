<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('pjutd_id', '!=', null)->first();
$pjutd = \App\Models\Pjutd::find($user->pjutd_id);

$canUpdate = $user->can('update', $pjutd);
echo "Can update: " . ($canUpdate ? 'yes' : 'no') . "\n";
