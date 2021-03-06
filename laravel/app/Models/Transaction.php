<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mutable;
use App\Models\TransactionDetail;

class Transaction extends Model
{
	use Eloquence, Mutable;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'transactions';

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

		'enroller',
		'enroller_id',
		'funnel_id',
		'tag_id',
		'tag',

		'product_id',
		'product_name',
		'product_amount',
		'product_discount',
		'amount',

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
		'ip_address',

		'response',
		'responsetext',
		'authcode',
		'transactionid',
		'orderid',
		'avsresponse',
		'cvvresponse',
		'type',
		'response_code'

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

	public function getCardExpMonthAttribute()
	{
		return $this->attributes['card_exp_month'] < 10 ? '0' . $this->attributes['card_exp_month'] : (string) $this->attributes['card_exp_month'];
	}

	public function getCardExpYearAttribute()
	{
		return $this->attributes['card_exp_year'] < 10 ? '0' . $this->attributes['card_exp_year'] : (string) $this->attributes['card_exp_year'];
	}

	public function getInfoAttribute()
	{
		return json_decode($this->attributes['info']);
	}

	public function detail()
	{
		return $this->hasMany(TransactionDetail::class, 'transaction_id', 'id');
	}

}