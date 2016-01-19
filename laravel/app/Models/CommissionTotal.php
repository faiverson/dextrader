<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionTotal extends Model
{
	protected $table = 'commissions_total';

	public $timestamps = false;

	protected $dates = ['created_at', 'updated_at'];

	protected $hidden = ['id'];

	protected $fillable = ['id', 'user_id', 'paid', 'ready', 'pending', 'holdback'];
}
