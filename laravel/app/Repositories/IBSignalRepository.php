<?php
namespace App\Repositories;

use App\Repositories\Contracts\LiveSignalRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\IBSignal;
use DateTime;

class IBSignalRepository extends AbstractRepository implements LiveSignalRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return IBSignal::class;
	}

	public function find_signal($mt_id, $trade)
	{
		return $this->model->where('mt_id', $mt_id)->where('trade_type', $trade)->first(['id']);
	}

	public function total($where)
	{
		$query = $this->model;
		$query = $this->addFilters($query, $where);
		return $query->count();
	}

	public function getSignals($select, $limit, $offset, $order_by, $where)
	{
		$query =  $this->model;
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

		$query = $this->addFilters($query, $where);
		return $query->get($select);
	}

	protected function addFilters($query, $where)
	{
		if(array_key_exists('from', $where) && $where['from'] != null) {
			$from = new DateTime($where['from']);
			$query = $query->whereDate('signal_time', '>=', $from);
		}

		if(array_key_exists('to', $where) && $where['to'] != null) {
			$to = new DateTime($where['to']);
			$query = $query->whereDate('signal_time', '<=', $to);
		}

		if(array_key_exists('trade_type', $where) && $where['trade_type'] != null) {
			$query = $query->where('trade_type', '=', $where['trade_type']);
		}

		if(array_key_exists('direction', $where) && $where['direction'] != null) {
			$query = $query->where('direction', '=', $where['direction']);
		}

		if(array_key_exists('winloss', $where) && $where['winloss'] != null) {
			$query = $query->where('winloss', '=', $where['winloss']);
		}

		return $query;
	}
}