<?php
namespace App\Repositories;

use App\Models\BillingAddress;
use App\Repositories\Contracts\BillingAddressRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;

class BillingAddressRepository extends AbstractRepository implements BillingAddressRepositoryInterface
{
	public function model()
	{
		return BillingAddress::class;
	}

	public function findUserAddress($user_id, $billing_address_id)
	{
		return $this->model->where('id', $billing_address_id)->where('user_id', $user_id)->first();
	}
}