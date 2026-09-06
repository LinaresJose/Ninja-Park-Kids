<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Backup diario automatico a las 3:00 AM (hora del servidor / UTC)
// El comando guarda los ultimos 7 dias y elimina backups mas antiguos automaticamente.
Schedule::command('db:backup')->dailyAt('03:00');
