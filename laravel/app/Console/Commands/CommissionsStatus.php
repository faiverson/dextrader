<?php

namespace App\Console\Commands;

use App\Gateways\CommissionGateway;
use App\Gateways\PaymentGateway;
use Illuminate\Console\Command;
use DB;
use Log;

class CommissionsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comms:pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the pending comms';

    /**
     * Create a new command instance.
     *
     * @return void
     */
	public function __construct(CommissionGateway $commissionGateway)
	{
		parent::__construct();
		$this->commissionGateway = $commissionGateway;
	}

    /**
     * Execute the console command.
     *
     * @return mixed
     */
	public function handle()
	{
		$this->info('Starting commission process');
		DB::beginTransaction();
		try {
			$comms = $this->commissionGateway->getPendingToReady();
			if($comms) {
				foreach($comms as $index => $commission) {
					$response = $this->commissionGateway->updateToReady($commission);
					if($response) {
						$this->info('Commission user: ' . $commission->user_id . ' processed');
					}
					else {
						$this->warn('user: ' . $commission->user_id);
						Log::info('user: ' . $commission->user_id);
					}
				}
			}
		} catch(\Exception $e) {
			DB::rollback();
			$this->warn('ERROR in comms:pending');
			Log::info('ERROR in comms:pending', (array) $e->getMessage());
		}
		DB::commit();
		$this->info('Commissions Finished - ' . $comms);
		Log::info('Commissions Finished');
	}
}
