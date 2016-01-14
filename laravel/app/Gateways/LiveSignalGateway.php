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

	public function all_signals($type, $limit = null, $offset = null, $order_by = null)
	{
		return $this->repository[$type]->all(array('*'), $limit, $offset, $order_by);
	}

	public function findByType($product, $id)
	{
		return $this->repository[$product]->find($id);
	}

	public function add($data, $type)
	{
		if( ! $this->createValidator->with($data)->passes() )
		{
			$this->errors = $this->createValidator->errors();
			return false;
		}

		return $this->repository[$type]->create($data);
	}

	public function edit($data, $id, $type)
	{
		$data['id'] = $id;
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
}