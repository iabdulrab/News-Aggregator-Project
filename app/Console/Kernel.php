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
        // Fetch news articles every hour
        $schedule->command('news:fetch')->hourly()->withoutOverlapping();
        
        // Or you can customize:
        // $schedule->command('news:fetch')->everyThreeHours()->withoutOverlapping();
        // $schedule->command('news:fetch --sources=newsapi,guardian')->everyTwoHours();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
