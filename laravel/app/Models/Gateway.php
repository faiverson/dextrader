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
	protected $table = 'gateway_transactions';

	protected $dates = ['created_at', 'updated_at'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'purchase_id',
		'subscription_id',
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