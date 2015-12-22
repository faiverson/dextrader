<?php namespace App\Gateways;

abstract class AbstractGateway {

	/**
	 * All
	 *
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function all()
	{
		return $this->repository->all();
	}

	/**
	 * Find
	 *
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function find($id)
	{
		return $this->repository->find($id);
	}

	/**
	 * Create
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function create(array $data)
	{
		if( ! $this->createValidator->with($data)->passes() )
		{
			$this->errors = $this->createValidator->errors();
			return false;
		}

		return $this->repository->create($data);
	}

	/**
	 * Update
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function update(array $data, $id)
	{
		if($data['username']) {
			unset($data['username']);
		}

		if( ! $this->updateValidator->with($data)->passes() )
		{
			$this->errors = $this->updateValidator->errors();
			return false;
		}

		return $this->repository->update($data, $id);
	}

	/**
	 * Delete
	 *
	 * @return boolean
	 */
	public function destroy($id)
	{
		return $this->repository->destroy($id);
	}

	public function errors()
	{
		return $this->errors;
	}

}