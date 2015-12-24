<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class Purchase extends Model
{
	use Eloquence, Mappable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchases';

	protected $dates = ['created_at', 'updated_at'];

	protected $maps = [
		'purchase_id' => 'id',
	];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'user_id',
		'transaction_id',
		'enroller_id',
		'funnel_id',
		'tag_id',
		'invoice_id',
		'subscription_id'
	];

	protected $appends = array('purchase_id');

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id', 'product_id', 'card_id', 'billing_address_id'];

	public function getPurchaseIdAttribute()
	{
		return $this->attributes['id'];
	}

	public function setInfoAttribute($value)
	{
		$this->attributes['info'] = json_encode($value);
	}

	public function card()
	{
		return $this->hasOne(CreditCard::class, 'id', 'card_id');
	}

	public function product()
	{
		return $this->hasOne(Product::class, 'id', 'product_id');
	}

	public function address()
	{
		return $this->hasOne(BillingAddress::class, 'id', 'billing_address_id');
	}

}