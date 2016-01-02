<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;
use App\Models\CreditCard;
use App\Models\BillingAddress;
use App\Models\Product;
use App\Models\User;

class Subscription extends Model
{
    use Eloquence, Mappable;

    protected $table = 'subscriptions';

	protected $maps = [
		'subscription_id' => 'id',
	];

	protected $fillable = ['user_id', 'product_id', 'card_id', 'billing_address_id', 'status', 'attempts_billing', 'amount', 'last_billing', 'next_billing', 'enroller_id'];

	protected $appends = array('subscription_id');

	protected $visible = array('card.number');

	protected $hidden = ['id', 'user_id', 'product_id', 'card_id', 'billing_address_id', 'created_at', 'updated_at'];

	public function getSubscriptionIdAttribute()
	{
		return $this->attributes['id'];
	}

	public function card()
	{
		return $this->hasOne(CreditCard::class, 'id', 'card_id');
	}

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
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
