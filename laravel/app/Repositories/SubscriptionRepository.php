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

	/**
	 * @param $attribute
	 * @param $value
	 * @param array $columns
	 * @return mixed
	 */
	public function getSubForBilling($from) {
		$query = $this->model;
		$query = $query->where('status', 'active');
		$query = $query->where('attempts_billing', '<', 3);
		$query = $query->whereDate('next_billing', '=', $from);
		$query = $query->whereDate('last_billing', '<', $from);
		return $query->with('user')->with('product')->with('card')->with('address')->get();
	}
}