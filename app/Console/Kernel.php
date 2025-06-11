<?php

namespace App\Console;

use App\Models\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
    $now = now();

    $eventsToStart = Event::where('scheduled_date', '<=', $now)
        ->where('status', 'upcoming')
        ->get();

    foreach ($eventsToStart as $event) {
        $event->update(['status' => 'in_progress']);
    }

    $eventsToComplete = Event::where('status', 'in_progress')
        ->whereRaw('DATE_ADD(scheduled_date, INTERVAL duration_minutes MINUTE) <= ?', [$now])
        ->get();

    foreach ($eventsToComplete as $event) {
        $event->update(['status' => 'completed']);
    }

})->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
