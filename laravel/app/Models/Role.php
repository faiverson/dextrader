<?php
namespace App\Models;
use Zizaco\Entrust\EntrustRole;

final class Role extends EntrustRole
{
	protected $hidden = ['name', 'description', 'active', 'updated_at', 'created_at', 'pivot'];
}
