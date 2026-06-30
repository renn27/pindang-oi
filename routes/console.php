<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('notifications:prune --days=30')->dailyAt('02:00');
Schedule::command('notifications:send-todo-reminders')
    ->twiceDailyAt(8, 15, 30)
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
Schedule::command('notifications:send-monthly-todo-recaps')
    ->monthlyOn(1, '08:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Sinkronisasi harian kolom legacy is_active di tabel pegawais berdasarkan status periods hari itu
Schedule::call(function () {
    $today = now()->toDateString();
    
    $activeIds = \Illuminate\Support\Facades\DB::table('pegawai_status_periods')
        ->where('status', 'Aktif')
        ->where('start_date', '<=', $today)
        ->where(function ($q) use ($today) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', $today);
        })
        ->pluck('id_pegawai')
        ->toArray();

    \Illuminate\Support\Facades\DB::table('pegawais')
        ->whereNull('deleted_at')
        ->whereIn('id_pegawai', $activeIds)
        ->update(['is_active' => true]);

    \Illuminate\Support\Facades\DB::table('pegawais')
        ->whereNull('deleted_at')
        ->whereNotIn('id_pegawai', $activeIds)
        ->update(['is_active' => false]);
})->dailyAt('00:01')->timezone('Asia/Jakarta');
