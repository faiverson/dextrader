<?php

namespace App\Console\Commands;

use App\Gateways\CommissionGateway;
use App\Gateways\PaymentGateway;
use Illuminate\Console\Command;
use Config;
use DB;
use League\Csv\Writer;
use Log;
use DateTime;

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

	protected $csv;

	protected $filename;

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
		$this->day = new DateTime('now');
		$this->filename = base_path() . '/resources/assets/csv/payments-' . $this->day->format('Y-m-d') . '.csv';
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
			$comms = $this->commissionGateway->getCommissionToPay($this->day);
			if($comms->count() > 0) {
				$this->createCSV();
				foreach ($comms as $commissions) {

					if ((float)$commissions->total <= (float)Config::get('dextrader.paid_limit')) {
						$payment = $this->paymentGateway->payCommission($commissions);
						if ($payment) {
							$response = $this->commissionGateway->updateToPaid($commissions);
							if (!$response) {
								$this->warn('Failed updateToPaid user: ' . $commissions->user_id);
								Log::info('Failed updateToPaid user: ' . $commissions->user_id, $this->commissionGateway->errors());
							}
							$this->addRowToCSV($commissions, $payment);
						} else {
							$this->warn('Failed payCommission user: ' . $commissions->user_id);
							Log::info('Failed payCommission user: ' . $commissions->user_id);
						}
					} else {
						$response = $this->commissionGateway->payCommissionOnNextDate($commissions);
						if (!$response) {
							$this->warn('Failed payCommissionOnNextDate user: ' . $commissions->user_id);
							Log::info('Failed payCommissionOnNextDate user: ' . $commissions->user_id);
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

	protected function createCSV()
	{
		$this->csv = Writer::createFromFileObject(new \SplFileObject($this->filename, 'w'));
		$header = [
			'user_id',
			'first_name',
			'last_name',
			'email',
			'username',
			'ledger_type',
			'amount',
			'paid_dt'
		];
		$this->csv->setDelimiter("\t"); //the delimiter will be the tab character
		$this->csv->setNewline("\r\n"); //use windows line endings for compatibility with some csv libraries
		$this->csv->setOutputBOM(Writer::BOM_UTF8); //adding the BOM sequence on output
		$this->csv->insertOne($header);
	}

	protected function addRowToCSV($commissions, $payment)
	{
		$this->csv->insertOne([
			$payment->user_id,
			$commissions->to->first_name,
			$commissions->to->last_name,
			$commissions->to->email,
			$commissions->to->username,
			$payment->ledger_type,
			$payment->amount,
			$payment->paid_dt
		]);
	}

	protected function outputCSV()
	{
		$this->csv->output($this->filename);
	}
}
