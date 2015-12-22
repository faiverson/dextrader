<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class City extends Model
{
    use Eloquence, Mappable;

    protected $table = 'cities';
}
