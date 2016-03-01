<?php

namespace App\Providers;

use App\Events\NewHitEvent;
use App\Events\NewLeadEvent;
use App\Events\NewPurchaseEvent;
use App\Models\Hit;
use App\Models\Lead;
use App\Models\Purchase;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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
		'App\Events\SubscriptionCancelEvent' => [
//			'App\Listeners\GetResponseInactiveListener',
			'App\Listeners\SubscriptionCancelListener'
		],
		'App\Events\SubscriptionReactiveEvent' => [
			'App\Listeners\GetResponseReactiveListener'
		],
		'App\Events\SubscriptionRenewedEvent' => [
			'App\Listeners\SubscriptionCommissionListener'
		],

		'App\Events\RefundEvent' => [
			'App\Listeners\RefundCommissionListener',
		],

		'App\Events\AddSignalEvent' => [],
		'App\Events\UpdateSignalEvent' => [],
		'App\Events\CommissionEvent' => [],
		'App\Events\SubscriptionFailEvent' => [],

    ];

	protected $subscribe = [
		'App\Listeners\EmailEventListener',
		'App\Listeners\MarketingStatsListener',
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

		Lead::created(function ($item) {
			Event::fire(new NewLeadEvent($item));
		});

		Hit::created(function ($item) {
			Event::fire(new NewHitEvent($item));
		});

		Purchase::created(function ($item) {
			Event::fire(new NewPurchaseEvent($item));
		});

	}
}
