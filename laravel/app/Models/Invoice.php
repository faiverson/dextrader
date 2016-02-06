<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mutable;
use Sofa\Eloquence\Mappable;
use App\Models\InvoiceDetail;

class Invoice extends Model
{
	use Eloquence, Mutable, Mappable;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'invoices';

	protected $dates = ['created_at', 'updated_at'];

	protected $maps = [
		'invoice_id' => 'id',
	];

	protected $appends = array('invoice_id');

	protected $fillable = [
		'user_id',
		'first_name',
		'last_name',
		'email',

		'enroller_id',
		'amount',

		'funnel_id',
		'tag_id',
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

	public function getInvoiceIdAttribute()
	{
		return $this->attributes['invoice_id'];
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

	public function detail()
	{
		return $this->hasMany(InvoiceDetail::class, 'invoice_id', 'id');
	}
}