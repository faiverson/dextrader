<?php namespace App\Repositories\Abstracts;

use App\Models\User;
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
		if($limit != null) {
			$this->model = $this->model->take($limit);
		}

		if($offset != null) {
			$this->model = $this->model->skip($offset);
		}

		if($order_by != null) {
			foreach($order_by as $column => $dir) {
				$this->model = $this->model->orderBy($column, $dir);
			}
		}

		return $this->model->get($columns);
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
	public function update(array $data, $id, $attribute="id") {
		return $this->model->where($attribute, '=', $id)->update($data);
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
	public function find($id, $columns = array('*'), $limit = null, $offset = null) {
		return $this->model->find($id, $columns);
	}

	/**
	 * @param $attribute
	 * @param $value
	 * @param array $columns
	 * @return mixed
	 */
	public function findBy($attribute, $value, $columns = array('*'), $limit = null, $offset = null) {
		return $this->model->where($attribute, '=', $value)->get($columns);
	}
}