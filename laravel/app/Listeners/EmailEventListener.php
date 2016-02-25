<?php

namespace App\Listeners;

use App\Repositories\CommissionRepository;
use App\Repositories\InvoiceDetailRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionDetailRepository;
use Illuminate\Container\Container as App;
use App\Gateways\UserGateway;
use Illuminate\Mail\Mailer;
use Config;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\TransactionRepository;

class EmailEventListener implements ShouldQueue
{
	use InteractsWithQueue;

	protected $app;
	protected $mailer;
	protected $from;
	protected $admin;
	protected $userGateway;

	public function __construct(App $app, Mailer $mailer, UserGateway $userGateway)
	{
		$this->app = $app;
		$this->mailer = $mailer;
		$this->from = Config::get('dextrader.from');
		$this->admin = Config::get('dextrader.admin');
		$this->userGateway = $userGateway;
	}

    public function onCheckout($event)
    {
		$tr = new TransactionRepository($this->app);
		$transaction_id = $event->data['orderid'];
		$transaction = $tr->findWith($transaction_id);
		$trans_detail_repo = new TransactionDetailRepository($this->app);

		// lets get the products name
		$transaction_detail = $trans_detail_repo->findBy('transaction_id', $transaction_id);
		if($transaction_detail->count() > 0) {
			$products = array_column($transaction_detail->toArray(), 'product_display_name');
			$products = implode(' - ', $products);
		}

		$this->mailer->send('emails.purchase', ['purchase' => $transaction, 'products' => $products], function ($message) use ($transaction, $products) {
			$message
				->from($this->admin, $this->from)
				->to($transaction->email, $transaction->first_name . ' '.$transaction->last_name)
				->subject('Your ' . $products .' payment receipt');
		});
    }

	public function onCommissions($event)
	{
		$comm = $event->commission;
		if($comm->type == 'parent') {
			$this->onCommissionsParent($event);
		}
		else {
			$invoice_detail_repo = new InvoiceDetailRepository($this->app);
			$from = $this->userGateway->find($comm->from_user_id);
			$to = $this->userGateway->find($comm->to_user_id);

			// lets get the products name
			$invoice_detail = $invoice_detail_repo->findBy('invoice_id', $comm->invoice_id);
			if($invoice_detail->count() > 0) {
				$products = array_column($invoice_detail->toArray(), 'product_display_name');
				$products = implode(' - ', $products);
			}

			$params = ['from' => $from, 'to' => $to, 'commission' => $comm, 'products' => $products];
			$this->mailer->send('emails.commission', $params, function ($message) use ($to, $comm) {
				$message
					->from($this->admin, $this->from)
					->to($to->email, $to->fullname)
					->subject('You Just Earned A $' . $comm->amount .' Commission!');
			});
		}
	}

	public function onCommissionsParent($event)
	{
		$comm = $event->commission;
		$commission_repo = new CommissionRepository($this->app);
		$invoice_detail_repo = new InvoiceDetailRepository($this->app);
		$commission = $commission_repo->findUserCommisionByInvoice($comm->invoice_id);
		$from = $this->userGateway->find($commission->from_user_id);
		$intermediate = $this->userGateway->find($comm->from_user_id);
		$to = $this->userGateway->find($comm->to_user_id);

		// lets get the products name
		$invoice_detail = $invoice_detail_repo->findBy('invoice_id', $comm->invoice_id);
		if($invoice_detail->count() > 0) {
			$products = array_column($invoice_detail->toArray(), 'product_display_name');
			$products = implode(' - ', $products);
		}

		$params = ['from' => $from, 'to' => $to, 'commission' => $comm, 'intermediate' => $intermediate, 'products' => $products];
		$this->mailer->send('emails.commission', $params, function ($message) use ($to, $comm, $intermediate) {
			$message
				->from($this->admin, $this->from)
				->to($to->email, $to->fullname)
				->subject($intermediate->fullname . ' just helped you earn $' . $comm->amount .' Commission!');
		});
	}

	public function onSubscriptionCancel($event)
	{
		$product_repo = new ProductRepository($this->app);
		$product = $product_repo->find($event->subscription->product_id);
		$user = $this->userGateway->find($event->subscription->user_id);
		$params = [
			'user' => $user,
			'product' => $product
		];
		$this->mailer->send('emails.subscription-cancel', $params, function ($message) use ($user, $product) {
			$message
				->from($this->admin, $this->from)
				->to($user->email, $user->fullname)
				->subject('Your ' . $product->display_name . ' has been cancelled!');
		});
	}

	public function onSubscriptionFailed($event)
	{
		$product_repo = new ProductRepository($this->app);
		$product = $product_repo->find($event->subscription->product_id);
		$user = $this->userGateway->find($event->subscription->user_id);
		$params = [
			'user' => $user,
			'product' => $product
		];

		$this->mailer->send('emails.subscription-fail', $params, function ($message) use ($user) {
			$message
				->from($this->admin, $this->from)
				->to($user->email, $user->fullname)
				->subject('URGENT, your payment has failed ' . $user->fullname . '!');
		});
	}

	public function onSubscriptionRenewed($event)
	{
		$invoice = $event->data;
		$user = $this->userGateway->find($invoice->user_id);
		$invoice_detail_repo = new InvoiceDetailRepository($this->app);
		$invoice_detail = $invoice_detail_repo->findBy('invoice_id', $invoice->id);
		if($invoice_detail->count() > 0) {
			$products = array_column($invoice_detail->toArray(), 'product_display_name');
			$products = implode(' - ', $products);
		}
		$params = [
			'user' => $user,
			'invoice' => $invoice,
			'invoice_detail' => $invoice_detail
		];

		$this->mailer->send('emails.subscription-renewed', $params, function ($message) use ($user, $products) {
			$message
				->from($this->admin, $this->from)
				->to($user->email, $user->fullname)
				->subject('Your ' . $products .' monthly payment receipt');
		});
	}

	public function onTransactionRefund($event)
	{
		$data = $event->data;
		$amount = $data['amount'];
		$invoice_detail_repo = new InvoiceDetailRepository($this->app);
		$invoice_detail = $invoice_detail_repo->findBy('invoice_id', $data['invoice_id']);
		if($invoice_detail->count() > 0) {
			$products = array_column($invoice_detail->toArray(), 'product_display_name');
			$products = implode(' - ', $products);
		}

		$user = $this->userGateway->find($data['user_id']);
		$params = [
			'user' => $user,
			'products' => $products,
			'amount' => $amount
		];

		$this->mailer->send('emails.refund', $params, function ($message) use ($user, $products, $amount) {
			$message
				->from($this->admin, $this->from)
				->to($user->email, $user->fullname)
				->subject('Your $' . $amount . ' ' . $products . ' payment has been refunded!');
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
		$events->listen('App\Events\SubscriptionCancelEvent', 'App\Listeners\EmailEventListener@onSubscriptionCancel');
		$events->listen('App\Events\SubscriptionFailEvent', 'App\Listeners\EmailEventListener@onSubscriptionFailed');
		$events->listen('App\Events\SubscriptionRenewedEvent', 'App\Listeners\EmailEventListener@onSubscriptionRenewed');
		$events->listen('App\Events\RefundEvent', 'App\Listeners\EmailEventListener@onTransactionRefund');
	}
}
