<?php
namespace App\Repositories;

use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Subscription;
use DateTime;


class SubscriptionRepository extends AbstractRepository implements SubscriptionRepositoryInterface
{
	public function model()
	{
		return Subscription::class;
	}

	public function findByUser($user_id, $columns = array('*'), $limit = null, $offset = null) {
		$query = $this->model->with('card')->with('product')->with('address')->where('status', 'active')->where('user_id', $user_id);

		if($limit != null) {
			$query = $query->take($limit);
		}

		if($offset != null) {
			$query = $query->skip($offset);
		}
		return $query->get($columns);
	}

	public function findProductByUser($product_id, $user_id) {
		$query = $this->model->with('card')->with('product')->with('address')
			->where('product_id', $product_id)
			->where('user_id', $user_id);

		return $query->first();
	}

	public function isOwner($user_id, $subscription_id) {
		return $this->model->where('status', 'active')->where('user_id', $user_id)->where('subscription_id', $subscription_id)->count();
	}

	/**
	 * @param $attribute
	 * @param $value
	 * @param array $columns
	 * @return mixed
	 */
	public function getSubForBilling(DateTime $from) {
		$query = $this->model;
		$query = $query->where('status', 'active');
		$query = $query->where('attempts_billing', '<', 3);
		$query = $query->whereDate('next_billing', '<=', $from);
		$query = $query->whereDate('last_billing', '<', $from);
		// if we attempted already, we need to check every 2 days precisely
		// if we don't attempted we can check updated_at directly
		$query = $query->where(function($q) use ($from) {
			$q->whereRaw('DATEDIFF(updated_at, "' . $from->format('Y-m-d') .'") <= 0');
			$q->where('attempts_billing', 0);
		})->orWhere(function($q) use ($from) {
			$q->whereRaw('DATEDIFF(DATE_ADD(updated_at, INTERVAL (attempts_billing * 2) DAY), "' . $from->format('Y-m-d') .'") = 0');
			$q->where('attempts_billing', '>', 0);
		});
		return $query->with('user')->with('product')->with('card')->with('address')->take(10)->get();
	}
}