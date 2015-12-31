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
//				if(array_key_exists('responsetext', $gateway) && strtolower($gateway['responsetext']) == 'success') {
//					$this->transaction->failed($gateway, $transaction->id);
//				}
				$response = $this->transaction->set($gateway, $transaction->id);
				if(!$response) {
					$this->warn('Error in transaction set');
					Log::info('Error in transaction set', (array) $this->transaction->errors());
					return false;
				} else {
					$data = array_merge($gateway, $data);
				}
				$this->info('Processed ' . $data['orderid']);
			}
		}
		$this->info('Finished billing proccess');
    }
}
