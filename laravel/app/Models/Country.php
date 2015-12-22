<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class Country extends Model
{
    use Eloquence, Mappable;

    protected $table = 'countries';
}
