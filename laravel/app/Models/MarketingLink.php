<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class MarketingLink extends Model
{
	use Eloquence, Mappable;

	protected $table = 'funnels';

	protected $hidden = ['id', 'updated_at', 'created_at'];

	protected $maps = [
		'funnel_id' => 'id',
	];

	protected $appends = array('funnel_id');

	protected $fillable = ['title', 'image', 'link', 'description', 'campaign_id'];

	public function getMarketingLinkIdAttribute()
	{
		return $this->attributes['id'];
	}

}
