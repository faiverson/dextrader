<?php

namespace App\Console\Commands;

use App\Gateways\CommissionGateway;
use App\Gateways\PaymentGateway;
use Illuminate\Console\Command;
use DB;
use League\Flysystem\Exception;
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
		$this->info('Starting comms:pending');
		DB::beginTransaction();
		try {
			$comms = $this->commissionGateway->getPendingToReady();
			if($comms) {
				foreach($comms as $index => $commission) {
					$response = $this->commissionGateway->updateToReady($commission);
					if($response) {
						$this->info('Commission user: ' . $commission->user_id . ' processed');
						Log::info('comms:pending user: ' . $commission->user_id. ' processed');
					}
					else {
						$this->warn('user: ' . $commission->user_id);
						Log::info('comms:pending user: ' . $commission->user_id);
					}
				}
			}
		} catch(\Exception $e) {
			DB::rollback();
			$this->warn('ERROR in comms:pending');
			Log::error('ERROR in comms:pending', (array) $e);
		}
		DB::commit();
		$this->info('comms:pending finished - ' . $comms);
		Log::info('comms:pending finished');
	}
}
