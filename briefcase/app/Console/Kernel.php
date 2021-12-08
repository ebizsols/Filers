<?php

namespace App\Console;

use App\Console\Commands\AddMenu;
use App\Console\Commands\AutoCreateRecurringExpenses;
use App\Console\Commands\AutoCreateRecurringInvoices;
use App\Console\Commands\AutoCreateRecurringTasks;
use App\Console\Commands\AutoStopTimer;
use App\Console\Commands\ClearNullSessions;
use App\Console\Commands\CreateTranslations;
use App\Console\Commands\HideCoreJobMessage;
use App\Console\Commands\RemoveSeenNotification;
use App\Console\Commands\SendAttendanceReminder;
use App\Console\Commands\SendAutoTaskReminder;
use App\Console\Commands\SendEventReminder;
use App\Console\Commands\SendProjectReminder;
use App\Console\Commands\UpdateExchangeRates;
use App\Console\Commands\SendInvoiceReminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UpdateExchangeRates::class,
        AutoStopTimer::class,
        SendEventReminder::class,
        SendProjectReminder::class,
        HideCoreJobMessage::class,
        SendAutoTaskReminder::class,
        CreateTranslations::class,
        AddMenu::class,
        AutoCreateRecurringInvoices::class,
        AutoCreateRecurringExpenses::class,
        ClearNullSessions::class,
        SendInvoiceReminder::class,
        RemoveSeenNotification::class,
        SendAttendanceReminder::class,
        AutoCreateRecurringTasks::class

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('update-exchange-rate')->daily();
        $schedule->command('auto-stop-timer')->dailyAt('23:59');
        $schedule->command('send-event-reminder')->everyMinute();
        $schedule->command('send-project-reminder')->daily();
        $schedule->command('hide-cron-message')->everyMinute();
        $schedule->command('send-auto-task-reminder')->daily();
        $schedule->command('recurring-invoice-create')->daily();
        $schedule->command('recurring-expenses-create')->daily();
        $schedule->command('clear-null-session')->hourly();
        $schedule->command('send-invoice-reminder')->daily();
        $schedule->command('queue:work --tries=3 --stop-when-empty')->withoutOverlapping();
        $schedule->command('delete-seen-notification')->daily();
        $schedule->command('send-attendance-reminder')->everyMinute();
        $schedule->command('recurring-task-create')->daily();


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

}
