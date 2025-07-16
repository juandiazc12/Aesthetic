<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\TestBookingNotification;

class Kernel extends ConsoleKernel
{
    /**
     * Lista de comandos de la consola
     *
     * @var array
     */
    protected $commands = [
        TestBookingNotification::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('bookings:update-completed')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
