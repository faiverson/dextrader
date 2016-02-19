<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\LiveSignalCreateValidator;
use App\Services\LiveSignalUpdateValidator;
use App\Repositories\IBSignalRepository;
use App\Repositories\FXSignalRepository;
use App\Repositories\NASignalRepository;

class LiveSignalGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(IBSignalRepository $IBSignalRepository,
								FXSignalRepository $FXSignalRepository,
								NASignalRepository $NASignalRepository,
								LiveSignalCreateValidator $createValidator,
								LiveSignalUpdateValidator $updateValidator)
	{
		$this->repository['ib'] = $IBSignalRepository;
		$this->repository['fx'] = $FXSignalRepository;
		$this->repository['na'] = $NASignalRepository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function allSignals($type, $limit = null, $offset = null, $order_by = null, $filters = null)
	{
		return $this->repository[$type]->getSignals(array('*'), $limit, $offset, $order_by, $filters);
	}

	public function totalSignals($type, $filters)
	{
		return $this->repository[$type]->total($filters);
	}

	public function findByType($product, $id)
	{
		return $this->repository[$product]->find($id);
	}

	public function add($data, $type)
	{
		if(!$this->exceptions($data, $type)) {
			$this->errors = ['Signal not allowed in our system'];
			return false;
		}

		if( ! $this->createValidator->with($data)->passes() )
		{
			$this->errors = $this->createValidator->errors();
			return false;
		}

		return $this->repository[$type]->create($data);
	}

	public function edit($data, $id, $type)
	{
		if( ! $this->updateValidator->with($data)->passes() )
		{
			$this->errors = $this->updateValidator->errors();
			return false;
		}

		return $this->repository[$type]->update($data, $id);
	}

	public function find_signal($mt_id, $trade, $type)
	{
		return $this->repository[$type]->find_signal($mt_id, $trade);
	}

	public function destroyByType($signal_id, $type)
	{
		return $this->repository[$type]->destroy($signal_id);
	}

	protected function exceptions($data, $type)
	{
		if($type === 'ib' && $data['trade_type'] == 'M5') {
			return false;
		}
		return true;
	}
}