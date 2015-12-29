<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
		'App\Events\CheckoutEvent' => [
			'App\Listeners\CheckoutCommissionListener',
//			'App\Listeners\CheckoutNotificationListener',
			'App\Listeners\CheckoutEmailListener',
		],
		'App\Events\CommissionEvent' => [
			'App\Listeners\CommissionEmailListener',
//			'App\Listeners\CommissionNotificationListener',
		],
		'App\Events\LiveSignalEvent' => [
			'App\Listeners\LiveSignalEmailListener',
//			'App\Listeners\LiveSignalNotificationListener',
		],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
