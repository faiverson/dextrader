<?php namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract {

    public function transform(User $user)
    {
        return [
			'id'       => $user->id,
			'fullname'     => $user->first_name . ' ' . $user->last_name,
			'firstname'     => $user->first_name,
			'lastname'     => $user->last_name,
			'username' => $user->username,
			'email'    => $user->email,
			'role'     => $user->roles->role,
            'created_at' => $user->created_at->format('Y-m-d')
        ];
    }
}
