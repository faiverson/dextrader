<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class MarketingLink extends Model
{
	use Eloquence, Mappable;

	protected $table = 'marketing_links';

	protected $hidden = ['id', 'updated_at', 'created_at'];

	protected $maps = [
		'marketing_link_id' => 'id',
	];

	protected $appends = array('marketing_link_id');

	public function getMarketingLinkIdAttribute()
	{
		return $this->attributes['id'];
	}

}
