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
}