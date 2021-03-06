<?php

namespace App\Console;

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
//        \App\Console\Commands\Inspire::class,
		\App\Console\Commands\GRCampaigns::class,
		\App\Console\Commands\MonthlyPayments::class,
		\App\Console\Commands\CommissionsStatus::class,
		\App\Console\Commands\CommissionsPayments::class
    ];

	protected $bootstrappers = [
		'Illuminate\Foundation\Bootstrap\DetectEnvironment',
		'Illuminate\Foundation\Bootstrap\LoadConfiguration',
//		'Illuminate\Foundation\Bootstrap\ConfigureLogging',
		'App\Bootstrap\ConfigureLogging',
		'Illuminate\Foundation\Bootstrap\HandleExceptions',
		'Illuminate\Foundation\Bootstrap\RegisterFacades',
		'Illuminate\Foundation\Bootstrap\SetRequestForConsole',
		'Illuminate\Foundation\Bootstrap\RegisterProviders',
		'Illuminate\Foundation\Bootstrap\BootProviders',
	];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
		// run the cronjob every 2 hours
		$path = storage_path('logs/payments-monthly-' . date('Y-m-d') . '.log');
		$schedule->command('payments:monthly')->withoutOverlapping()->cron('0 */2 * * * *')->sendOutputTo($path);
		// 0 0,2,4,6,8,10,12,14,16,18,20,22 * * * make the precise hs

		$path = storage_path('logs/comms-pending-' . date('Y-m-d') . '.log');
		$schedule->command('comms:pending')->withoutOverlapping()->cron('0 1,3,5,7,9,11,14,15,17,19,21,23 * * *')->sendOutputTo($path);

		$path = storage_path('logs/comms-weekly-' . date('Y-m-d') . '.log');
		$schedule->command('comms:weekly')->weekly()->fridays()->at('23:30')->sendOutputTo($path);
    }
}
