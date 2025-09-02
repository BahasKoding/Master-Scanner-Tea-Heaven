<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Clean up old log files daily at 2 AM
        $schedule->command('logs:clear --days=7 --force')
                 ->daily()
                 ->at('02:00')
                 ->description('Clean up log files older than 7 days');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    // protected $commands = [
    //     \App\Console\Commands\FinishedGoodsSanityCheck::class,
    // ];
    // nanti diaktifkan kalau emg dibutuhkan/sudah beres semua
}
