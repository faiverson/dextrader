<?php

namespace App\Listeners;

use App\Gateways\UserGateway;
use Snowfire\Beautymail\Beautymail;
use Config;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailEventListener implements ShouldQueue
{
	public function __construct(Beautymail $bm, UserGateway $userGateway)
	{
		$this->mailer = $bm;
		$this->from = Config::get('dextrader.from');
		$this->admin = Config::get('dextrader.admin');
		$this->userGateway = $userGateway;
	}

    public function onCheckout($event)
    {
		dd($event->data);
		$event->data['email'] = 'fa.iverson@gmail.com';
		$this->mailer->send('emails.purchase', ['purchase' => $event->data], function ($message) use ($event) {
			$message
				->from($this->from)
				->to($event->data['email'])
				->subject('Yey! Your purchase has been approved!');
		});
    }

	public function onCommissions($event)
	{
		$user = $this->userGateway->find($event->commission->to_user_id);
		$user->email = 'fa.iverson@gmail.com';
		$this->mailer->send('emails.commission', ['user' => $user->toArray()], function ($message) use ($user) {
			$message
				->from($this->from)
				->to($user->email)
				->subject('Yey! You have a new commission!');
		});
	}

	public function onLiveSignal($event)
	{
		$this->mailer->send('emails.live-signal', $event->data, function ($message) use ($event) {
			$message
				->from($this->from)
				->to($this->admin)
				->subject('Live Signal on DexTrader!');
		});
	}

	/**
     * Handle the event.
     *
     * @param  LiveSignalEvent  $event
     * @return void
     */
	public function subscribe($events)
	{
		$events->listen(
			'App\Events\CheckoutEvent',
			'App\Listeners\EmailEventListener@onCheckout'
		);

		$events->listen(
			'App\Events\CommissionEvent',
			'App\Listeners\EmailEventListener@onCommissions'
		);

		$events->listen(
			'App\Events\LiveSignalEvent',
			'App\Listeners\EmailEventListener@onLiveSignal'
		);
	}
}
