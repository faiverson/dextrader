<?php

namespace App\Console\Commands;

use App\Gateways\SubscriptionGateway;
use App\Gateways\TransactionGateway;
use Illuminate\Console\Command;
use Log;


class MonthlyPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Task to run every month for the monthly billings';

	protected $subscription;

	protected $transaction;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SubscriptionGateway $subscriptionGateway, TransactionGateway $transactionGateway)
    {
        parent::__construct();
		$this->subscription = $subscriptionGateway;
		$this->transaction = $transactionGateway;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$this->info('Starting billing proccess');
		$subs = $this->subscription->getBillings('today');
		if($subs->count() > 0) {
			foreach($subs as $subscription) {
				$data = $this->subscription->setDataForTransaction($subscription);
				$transaction = $this->transaction->create($data);
				if(!$transaction) {
					$this->warn('Error in transaction create');
					Log::info('Error in transaction create', (array) $this->transaction->errors());
					return false;
				}
				$data['orderid'] = $transaction->id;
				$gateway = $this->transaction->gateway($data);
				$response = $this->transaction->set($gateway, $transaction->id);
				if(!$response) {
					$this->warn('Error in transaction set');
					Log::info('Error in transaction set', (array) $this->transaction->errors());
					return false;
				} else {
					$data = array_merge($gateway, $data);
				}

				if(array_key_exists('responsetext', $gateway) && strtolower($gateway['responsetext']) == 'success') {
					$response = $this->subscription->renewed($subscription);
					if(!$response) {
						$this->warn('Error in subscription renewed');
						Log::info('Error in subscription renewed', (array) $this->subscription->errors());
						return false;
					}

					$response = $this->transaction->generateInvoice(array_merge($data, [
						'subscription_id' => $subscription->id,
						'card_id' => $subscription->card_id,
						'billing_address_id' => $subscription->billing_address_id,
						'status' => 'active',
						'transaction_id' => $data['orderid']
					]));
					if(!$response) {
						$this->warn('Error in create invoice');
						Log::info('Error in create invoice', (array) $this->transaction->errors());
						return false;
					}
				}
				else {
					$response = $this->subscription->failed($subscription);
					if(!$response) {
						$this->warn('Error in subscription failed');
						Log::info('Error in subscription failed', (array) $this->subscription->errors());
						return false;
					}
					else {
						Log::info('Subscription failed', $this->subscription->toArray());
						$this->warn('Subscription failed');
					}
				}

				Log::info('Processed order ID: ' . $data['orderid']);
				$this->info('Processed order ID: ' . $data['orderid']);
			}
		}
		$this->info('Finished billing proccess');
    }
}
