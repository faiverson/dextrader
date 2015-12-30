<?php

namespace App\Listeners;

use Illuminate\Container\Container as App;
use App\Gateways\UserGateway;
use App\Models\Transaction;
use Snowfire\Beautymail\Beautymail;
use Config;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\TransactionRepository;

class EmailEventListener //implements ShouldQueue
{
	public function __construct(App $app, Beautymail $bm, UserGateway $userGateway)
	{
		$this->app = $app;
		$this->mailer = $bm;
		$this->from = Config::get('dextrader.from');
		$this->admin = Config::get('dextrader.admin');
		$this->userGateway = $userGateway;
	}

    public function onCheckout($event)
    {
		$tr = new TransactionRepository($this->app);
		$transaction_id = $event->data['orderid'];
		$transaction = $tr->findWith($transaction_id);
//		$transaction->email = 'fa.iverson@gmail.com';
		$this->mailer->send('emails.purchase', ['purchase' => $transaction], function ($message) use ($transaction) {
			$message
				->from($this->from)
				->to($transaction->email)
				->subject('Yey! Your purchase has been approved!');
		});
    }

	public function onCommissions($event)
	{
		$from = $this->userGateway->find($event->commission->from_user_id);
		$to = $this->userGateway->find($event->commission->to_user_id);
		$params = ['from' => $from, 'to' => $to];
//		$to->email = 'fa.iverson@gmail.com';
		$this->mailer->send('emails.commission', $params, function ($message) use ($to) {
			$message
				->from($this->from)
				->to($to->email)
				->subject('Yey! You have a new commission!');
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
		$events->listen('App\Events\CheckoutEvent', 'App\Listeners\EmailEventListener@onCheckout');
		$events->listen('App\Events\CommissionEvent', 'App\Listeners\EmailEventListener@onCommissions');
	}
}
