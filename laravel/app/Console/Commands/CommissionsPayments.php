<?php

namespace App\Console\Commands;

use App\Gateways\CommissionGateway;
use App\Gateways\PaymentGateway;
use Illuminate\Console\Command;
use Config;
use DB;
use Log;

class CommissionsPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comms:weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commissions\' payments weekly';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(CommissionGateway $commissionGateway, PaymentGateway $paymentGateway)
	{
		parent::__construct();
		$this->commissionGateway = $commissionGateway;
		$this->paymentGateway = $paymentGateway;
	}

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$this->info('Starting commission payment process');
		DB::beginTransaction();
		try {
			$comms = $this->commissionGateway->getCommissionToPay();
			if($comms->count() > 0) {
				foreach ($comms as $commissions) {
					if ($commissions->total >= Config::get('dextrader.paid_limit')) {
						$response = $this->paymentGateway->payCommission($commissions);
						if ($response) {
							$response = $this->commissionGateway->updateToPaid($commissions);
							if (!$response) {
								$this->warn('Failed updateToPaid user: ' . $commissions->user_id);
								Log::info('Failed updateToPaid user: ' . $commissions->user_id, $this->commissionGateway->errors());
							}
						} else {
							$this->warn('Failed payCommission user: ' . $commissions->user_id);
							Log::info('Failed payCommission user: ' . $commissions->user_id);
						}
					} else {
						$response = $this->commissionGateway->payNextWeekCommission($commissions);
						if (!$response) {
							$this->warn('Failed payNextWeekCommission user: ' . $commissions->user_id);
							Log::info('Failed payNextWeekCommission user: ' . $commissions->user_id);
						}
					}
					$this->info('Commission user: ' . $commissions->user_id);
				}
			}
			else {
				$this->info('No users found');
				Log::info('No users found in comms:weekly');
			}
		} catch(\Exception $e) {
			DB::rollback();
			$this->warn('ERROR in comms:weekly');
			Log::info('ERROR in comms:weekly', (array) $e->getMessage());
		}
		DB::commit();
		$this->info('Finished commission payment proccess');
    }
}
