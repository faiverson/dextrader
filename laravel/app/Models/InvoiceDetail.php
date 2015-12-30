<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mutable;


class InvoiceDetail extends Model
{
	use Eloquence, Mutable;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'invoices_detail';

	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'invoice_id',
		'product_id',
		'product_name',
		'product_display_name',
		'product_amount',
		'product_discount',
		'subscription_id'
	];

	public function getProductAmountAttribute()
	{
		return number_format($this->attributes['product_amount'], 2, '.', ',');
	}

	public function setProductAmountAttribute($value)
	{
		$this->attributes['product_amount'] = number_format($value, 2, '.', '');
	}
}