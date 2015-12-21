<?php namespace App\Libraries\dtrader;

/**
 * RepositoryInterface provides the standard functions to be expected of ANY
 * repository.
 */
interface RepositoryInterface {

	public function all($columns = array('*'), $limit = null, $offset = null);

	public function find($id, $columns = array('*'), $limit = null, $offset = null);

	public function findBy($field, $value, $columns = array('*'), $limit = null, $offset = null);

	public function create(array $attributes);

	public function update(array $attributes);

	public function destroy($ids);

}