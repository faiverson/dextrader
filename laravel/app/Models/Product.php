<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class Product extends Model
{
	use Eloquence, Mappable, SoftDeletes;

	protected $table = 'products';

	protected $hidden = ['id', 'name', 'updated_at', 'created_at', 'deleted_at'];

	protected $fillable = ['name', 'display_name', 'amount', 'discount', 'active', 'parents', 'period'];

	protected $maps = [
		'product_id' => 'id',
	];

	protected $appends = array('product_id');

	public function getProductIdAttribute()
	{
		return $this->attributes['id'];
	}

	public function getAmountAttribute()
	{
		return number_format($this->attributes['amount'], 2, '.', ',');
	}

	public function setAmountAttribute($value)
	{
		$this->attributes['amount'] = number_format($value, 2, '.', '');
	}
}
