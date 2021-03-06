<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;
use Config;

class Commission extends Model
{
	use Eloquence, Mappable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'commissions';

	protected $dates = ['created_at', 'updated_at'];

	protected $maps = [
		'commission_id' => 'id',
	];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'amount', 'from_user_id', 'to_user_id', 'invoice_id', 'payout_dt', 'holdback', 'refund_dt', 'status', 'type', 'holdback_paid', 'refund_by',  'created_at', 'updated_at'];

	protected $appends = array('commission_id');

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id'];

	public function getPurchaseIdAttribute()
	{
		return $this->attributes['id'];
	}

	public function setAmountAttribute($value)
	{
		$this->attributes['amount'] = number_format($value, 2, '.', '');
		$this->attributes['holdback'] = number_format($value * Config::get('dextrader.holdback'), 2, '.', '');
	}

	public function from()
	{
		return $this->hasOne(User::class, 'id', 'from_user_id')->select(['user_id', 'first_name', 'last_name', 'username', 'email']);
	}

	public function to()
	{
		return $this->hasOne(User::class, 'id', 'to_user_id')->select(['user_id', 'first_name', 'last_name', 'username', 'email']);
	}

	public function products()
	{
		$query = $this->hasMany(InvoiceDetail::class, 'invoice_id', 'invoice_id');
		return $query->select(['invoice_id', 'invoices_detail.product_display_name', 'invoices_detail.product_amount']);
	}

	public function active()
	{
		return $this->hasOne(User::class, 'id', 'to_user_id')->where('users.active', 1)->select(['user_id', 'first_name', 'last_name', 'username', 'email']);
	}

	public function getAmountAttribute()
	{
		return number_format($this->attributes['amount'], 2, '.', ',');
	}

	public function getHoldbackAttribute()
	{
		return number_format($this->attributes['holdback'], 2, '.', ',');
	}

}