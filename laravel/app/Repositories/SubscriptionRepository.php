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

	public function findByUser($user_id, $columns = array('*'), $limit = null, $offset = null) {
		return $this->model->with('card')->with('product')->with('address')->where('user_id', $user_id)->get($columns);
	}

	public function isOwner($user_id, $subscription_id) {
		return $this->model->where('user_id', $user_id)->where('subscription_id', $subscription_id)->count();
	}
}