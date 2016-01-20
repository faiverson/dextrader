<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class Training extends Model
{
	use Eloquence, Mappable;

	protected $table = 'trainings';

	protected $hidden = ['id', 'type', 'updated_at', 'created_at'];

	protected $maps = [
		'training_id' => 'id',
	];

	protected $fillable	 = ['video_id', 'type', 'title', 'filename', 'description', 'time', 'unlock_at'];

	protected $appends = array('training_id');

	public function getTrainingIdAttribute()
	{
		return $this->attributes['id'];
	}
}
