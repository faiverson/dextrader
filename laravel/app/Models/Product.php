<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class Product extends Model
{
	use Eloquence, Mappable;

	protected $table = 'products';

	protected $hidden = ['id', 'name', 'updated_at', 'created_at', 'deleted_at'];

	protected $maps = [
		'product_id' => 'id',
	];

	protected $appends = array('product_id');

	public function getProductIdAttribute()
	{
		return $this->attributes['id'];
	}
}
