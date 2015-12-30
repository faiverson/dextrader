<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mutable;


class TransactionDetail extends Model
{
	use Eloquence, Mutable;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'transactions_detail';

	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'transaction_id',
		'product_id',
		'product_name',
		'product_display_name',
		'product_amount',
		'product_discount'
	];

	public function getProductAmountAttribute()
	{
		return number_format($this->attributes['product_amount'], 2, '.', ',');
	}

	public function setProductAmountAttribute($value)
	{
		$this->attributes['product_amount'] = number_format($value, 2, '.', '');
	}

	public function getAmountAttribute()
	{
		return number_format($this->attributes['amount'], 2, '.', ',');
	}

	public function setAmountAttribute($value)
	{
		$this->attributes['amount'] = number_format($value, 2, '.', '');
	}

	public function setInfoAttribute($value)
	{
		$this->attributes['info'] = json_encode($value);
	}

	public function getInfoAttribute()
	{
		return json_decode($this->attributes['info']);
	}
}