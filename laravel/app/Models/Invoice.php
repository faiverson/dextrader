<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mutable;


class Invoice extends Model
{
	use Eloquence, Mutable;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'invoices';

	protected $dates = ['created_at', 'updated_at'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'first_name',
		'last_name',
		'email',

		'enroller_id',
		'amount',

		'product_id',
		'product_name',
		'product_amount',
		'product_discount',

		'funnel_id',
		'tag_id',
		'subscription_id',
		'transaction_id',

		'billing_address_id',
		'billing_address',
		'billing_address2',
		'billing_city',
		'billing_state',
		'billing_country',
		'billing_zip',
		'billing_phone',

		'card_id',
		'card_name',
		'card_exp_month',
		'card_exp_year',
		'card_network' ,
		'card_first_six',
		'card_last_four',
		'info',
		'ip_address'

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