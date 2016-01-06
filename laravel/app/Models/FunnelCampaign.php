<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FunnelCampaign extends Model
{
	protected $table = 'funnel_campaigns';

	protected $fillable = ['title'];
}
