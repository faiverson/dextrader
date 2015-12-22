<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class Subscription extends Model
{
    use Eloquence, Mappable;

    protected $table = 'subscriptions';

	protected $fillable = ['user_id', 'product_id', 'card_id', 'billing_address_id', 'status', 'attempts_billing', 'amount', 'last_billing', 'next_billing', 'enroller_id'];
}
