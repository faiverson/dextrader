<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Mappable;

class UserSettings extends Model
{
	protected $table = 'users_settings';

	public $timestamps = false;

	protected $fillable = ['user_id', 'key', 'value'];

	protected $hidden = ['id'];
}