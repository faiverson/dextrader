<?php
namespace App\Repositories;

use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Subscription;

class SubscriptionRepository extends AbstractRepository implements SubscriptionRepositoryInterface
{
	public function model()
	{
		return Subscription::class;
	}
}