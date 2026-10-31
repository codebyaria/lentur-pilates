<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal demo relatif terhadap hari ini, jadi demo live mulai segar setiap hari.
Schedule::command('migrate:fresh --seed --force')
    ->dailyAt('03:00')
    ->timezone('Asia/Jakarta')
    ->environments('production')
    ->when(fn () => config('app.demo'));
