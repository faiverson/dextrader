<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class Gateway extends Model
{
	use Eloquence, Mappable;

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
		'enroller_id',
		'funnel_id',
		'tag_id',

		'product_id',
		'product_name',
		'product_amount',
		'product_discount',
		'amount',

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

		'status',
		'authcode',
		'transactionid',
		'orderid',
		'avsresponse',
		'cvvresponse',
		'type',
		'response_code'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['id'];
}