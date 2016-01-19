<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;

class SpecialOffer extends Model
{
    use Eloquence;

    protected $table = 'special_offers';

	protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

	protected $appends = array('offer_id');

	protected $fillable = [
		'funnel_id',
		'product_id',
		'amount',
		'ending_dt',
		'countdown',
		'type'
	];

	public function getOfferIdAttribute()
	{
		return $this->attributes['id'];
	}
}
