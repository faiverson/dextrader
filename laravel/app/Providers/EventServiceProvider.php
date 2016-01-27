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
			'App\Listeners\GetResponseBuyersListener'
		],
		'App\Events\CheckoutFailedEvent' => [
			'App\Listeners\GetResponsePartialListener'
		],
		'App\Events\CommissionEvent' => [
		],
		'App\Events\SubscriptionCancelEvent' => [
			'App\Listeners\GetResponseInactiveListener'
		],
		'App\Events\SubscriptionFailEvent' => [
		],
		'App\Events\SubscriptionRenewedEvent' => [
			'App\Listeners\SubscriptionCommissionListener'
		],
		'App\Events\AddSignalEvent' => [
		],
		'App\Events\UpdateSignalEvent' => [
		],
    ];

	protected $subscribe = [
		'App\Listeners\EmailEventListener',
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
