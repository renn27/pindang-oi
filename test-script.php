<?php
$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$data = app(\App\Services\DashboardAnalyticsService::class)->rankPegawai(5);
echo json_encode($data->items()[0], JSON_PRETTY_PRINT);
