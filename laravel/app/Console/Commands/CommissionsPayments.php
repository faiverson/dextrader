<?php

namespace App\Console\Commands;

use App\Gateways\CommissionGateway;
use Illuminate\Console\Command;


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
		$this->info('Starting commission payment proccess');
		$comms = $this->commissionGateway->getCommissionToPay();
		$this->paymentGateway->getCommissionToPay($comms);
		dd($comms->toArray());
    }
}
