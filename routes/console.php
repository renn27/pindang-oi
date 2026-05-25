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
