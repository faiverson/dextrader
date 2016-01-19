<?php namespace App\Repositories\Abstracts;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;

/**
 * Class Repository
 * @package Bosnadev\Repositories\Eloquent
 */
abstract class Repository implements RepositoryInterface {

	/**
	 * @var App
	 */
	private $app;

	/**
	 * @var
	 */
	protected $model;

	/**
	 * @param App $app
	 */
	public function __construct(App $app) {
		$this->app = $app;
		return $this->model = $app->make($this->model());
	}

	/**
	 * Specify Model class name
	 *
	 * @return mixed
	 */
	abstract function model();

	/**
	 * @param array $columns
	 * @return mixed
	 */
	public function all($columns = array('*'), $limit = null, $offset = null, $order_by = null) {
		$query = $this->model;
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

		return $query->get($columns);
	}

	/**
	 * @param array $data
	 * @return mixed
	 */
	public function create(array $data) {
		return $this->model->create($data);
	}

	/**
	 * @param array $data
	 * @param $id
	 * @param string $attribute
	 * @return mixed
	 */
	public function update(array $data, $id) {
		$data = $this->setAttributtes($data);
		return $this->model->find($id)->update($data);
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function destroy($id) {
		return $this->model->destroy($id);
	}

	/**
	 * @param $id
	 * @param array $columns
	 * @return mixed
	 */
	public function find($id, $columns = array('*')) {
		return $this->model->find($id, $columns);
	}

	/**
	 * @param $attribute
	 * @param $value
	 * @param array $columns
	 * @return mixed
	 */
	public function findBy($attribute, $value, $columns = array('*'), $limit = null, $offset = null, $order_by = null) {
		$query = $this->model;
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
		return $query->where($attribute, '=', $value)->get($columns);
	}

	protected function setAttributtes(array $data)
	{
		$data = array_map('trim', $data);
		$data = array_filter($data);
		return $data;
	}
}