<?php
namespace App\Repositories;

use App\Repositories\Contracts\StatRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\MarketingStat;
use DateTime;
use DB;

class StatRepository extends AbstractRepository implements StatRepositoryInterface
{
	public function model()
	{
		return MarketingStat::class;
	}

	public function findByUser($user_id, $funnel_id, $tag_id, $created_at)
	{
		return $this->model->where('user_id', $user_id)
			->where('funnel_id', $funnel_id)
			->where('tag_id', $tag_id)
			->whereDate('created_at', '=', $created_at)
			->first();
	}

	public function getMarketingStats($user_id, $limit, $offset, $order_by, $where)
	{
		$query =  $this->model->where('user_id', $user_id);
		$query = $this->filters($query, $where);
		if($limit != null) {
			$query = $query->take($limit);
		}

		if($offset != null) {
			$query = $query->skip($offset);
		}

		if($order_by != null) {
			foreach($order_by as $column => $dir) {
				$query = $query->orderBy($column, $dir);
			}
		}

		return $query->groupBy('funnel_id')
			->groupBy('tag_id')
			->get();
	}

	public function getTotalMarketingStats($user_id, $where)
	{
		$sq = $this->model->where('user_id', $user_id);
		$sq = $this->filters($sq, $where);
		$sq = $sq->groupBy('funnel_id')
			->groupBy('tag_id');

		$params = array_merge([$user_id] , array_values($where));
		$query = DB::select('SELECT count(*) as total from (' . $sq->toSql() . ') as totals', $params);
		return $query[0]->total;
	}

	protected function filters($query, $where)
	{
		foreach($where as $key => $w) {
			switch($key) {
				case 'from':
					$from = new DateTime($w);
					$query = $query->whereDate('created_at', '>=', $from);
					break;
				case 'to':
					$from = new DateTime($w);
					$query = $query->whereDate('created_at', '<=', $from);
					break;
				case 'funnel':
					$query = $query->where('funnel', $w);
					break;
				case 'tag':
					$query = $query->where('tag', $w);
					break;
			}
		}
//		if(array_key_exists('from', $where) && $where['from'] != null) {
//			$from = new DateTime($where['from']);
//			$query = $query->whereDate('created_at', '>=', $from);
//		}
//
//		if(array_key_exists('to', $where) && $where['to'] != null) {
//			$from = new DateTime($where['to']);
//			$query = $query->whereDate('created_at', '<=', $from);
//		}
//
//		if(array_key_exists('funnel', $where) && $where['funnel'] != null) {
//			$query = $query->where('funnel', $where['funnel']);
//		}
//
//		if(array_key_exists('tag', $where) && $where['tag'] != null) {
//			$query = $query->where('tag', $where['tag']);
//		}

		return $query;
	}

}