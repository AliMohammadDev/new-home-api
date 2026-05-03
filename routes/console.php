<?php

use App\Jobs\ArchiveDailyCompanyEntries;
use App\Jobs\ArchiveDailyExpenseEntries;
use App\Jobs\ArchiveDailyPersonalWithdrawalEntries;
use App\Jobs\ArchiveDailySalesPointEntries;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
  $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*
|--------------------------------------------------------------------------
| Task Scheduling
|--------------------------------------------------------------------------
|
| Here you can define your scheduled jobs and commands.
| These tasks will run automatically based on the frequency defined.
|
*/

/**
 * Archive daily company entries at midnight.
 */
Schedule::job(new ArchiveDailyCompanyEntries)->daily();

/**
 * Archive daily expense entries every minute.
 * Note: Check if this frequency is intended for production.
 */
Schedule::job(new ArchiveDailyExpenseEntries)->daily();

/**
 * Archive daily personal withdrawal entries every minute.
 */
Schedule::job(new ArchiveDailyPersonalWithdrawalEntries)->daily();

/**
 * Archive daily sales point entries every minute.
 */
Schedule::job(new ArchiveDailySalesPointEntries)->daily();

Schedule::command('queue:work --stop-when-empty')
  ->everyMinute()
  ->withoutOverlapping();
